<?php

if (! class_exists('My_Youtube_Recommendation_Study_Json')) {
  class My_Youtube_Recommendation_Study_Json
  {

    private $channel_id;
    private $expiration; // in hours
    private $filename;
    private $dirname;
    private $path;

    public function __construct(
      string $channel_id, 
      int    $expiration, 
      string $dirname, 
      string $filename
    ) {
      $this->channel_id = $channel_id;
      $this->expiration = $expiration;
      $this->dirname    = $dirname;
      $this->filename   = $filename;
      $this->path       = $this->create_folder_path(); 

      //Registro da action do Ajax no Wordpress
      $ajax_action = 'my_youtube_recommendation_study_videos';
      add_action("wp_ajax_{$ajax_action}", array ($this, 'write_content'));
      add_action("wp_ajax_nopriv_{$ajax_action}", array ($this, 'write_content'));
    }// end:__construct

    private function get_filename_full_path()
    {
      return $this->path . '/' . $this->filename;
    }// end:get_filename_full_path

    private function create_folder_path()
    {
      $upload_dir = wp_upload_dir();
      if (! empty($upload_dir['basedir'])) {
        $dirname = $upload_dir['basedir'] . '/' . $this->dirname;
        if (! file_exists($dirname)) {
          wp_mkdir_p($dirname);
        }
        return $dirname;
      }
    }// end:create_folder_path

    private function from_youtube_feed()
    {
      $channel_id = $this->channel_id;
      $feed_url   = "https://www.youtube.com/feeds/videos.xml?channel_id={$channel_id}";
      $response   = wp_remote_get($feed_url);
      $content    = wp_remote_retrieve_body($response);
      $content    = preg_replace('/<(\/)?(yt|media)\:/i', '<$1$2_', $content);
      $xml        = simplexml_load_string($content, "SimpleXMLElement", LIBXML_NOCDATA);

      // Quick Convertion XML to Array (using json encoding)
      $json       = json_encode($xml);
      $content    = json_decode($json, true);

      $videos     = array();
      $count      = 0;

      foreach ($content['entry'] as $item) {

        // Channel info
        if ( $count == 0 ) {
          $videos['channel']           = $item['author'];
          $videos['channel']['avatar'] = $this->get_channel_avatar();
        }

        // Videos list
        $yt_video_id = $item['yt_videoId'];
        $videos['videos'][$count]['id']        = $yt_video_id;
        $videos['videos'][$count]['link']      = "https://youtu.be/{$yt_video_id}";
        $videos['videos'][$count]['thumbnail'] = "https://img.youtube.com/vi/{$yt_video_id}/mqdefault.jpg";
        $videos['videos'][$count]['title']     = $item['title'];
        $videos['videos'][$count]['published'] = $item['published'];
        $videos['videos'][$count]['rating']    = $item['media_group']['media_community']['media_starRating']['@attributes']['average'];
        $videos['videos'][$count]['likes']     = $item['media_group']['media_community']['media_starRating']['@attributes']['count'];
        $videos['videos'][$count]['views']     = $item['media_group']['media_community']['media_statistics']['@attributes']['views'];
    
        $count++;
      }
      return json_encode($videos);
    }// end:from_youtube_feed

    private function get_channel_avatar()
    {
      $channel_id  = $this->channel_id;
      $channel_url = "https://m.youtube.com/channel/{$channel_id}";
      $response    = wp_remote_get($channel_url);
      $content     = wp_remote_retrieve_body($response);
      $http_code   = wp_remote_retrieve_response_code($response);

      if ($http_code != 200) {
        return;
      }
      
      $pattern = '/name="twitter:image" content="([^"]*)"/i';
      preg_match($pattern, $content, $matches);

      if ($matches[1]) {
        $avatar = $matches[1];
      }
      return $avatar;
    }// end:get_channel_avatar

    private function from_file()
    {
      $json_path = $this->get_filename_full_path();
      $json = file_get_contents($json_path);
      return $json;
    }// end:from_file

    private function save_file($json_content)
    {
      $json_path = $this->get_filename_full_path();
      $fp = fopen($json_path, 'w');
      fwrite($fp, $json_content);
      fclose($fp);
    }// end:save_file

    private function is_expired()
    {
      $expiration_time   = $this->expiration * 3600;
      $json_file         = $this->get_filename_full_path();
      $json_file_expired = (time() - filemtime($json_file) > $expiration_time);

      return ($json_file_expired);
    }// end:is_expired

    private function get_content()
    {
      if ($this->is_expired()) {
        // Catch from YouTube Feed
        $json_content = $this->from_youtube_feed();
        $this->save_file($json_content);
      } else {
        // Catch from Local Json File
        $json_content = $this->from_file();
      }

      return $json_content;
    }// end:get_content

    public function write_content()
    {
      echo $this->get_content();
      wp_die();
    }// end:write_content

  }// end:My_Youtube_Recommendation_Study_Json 
}