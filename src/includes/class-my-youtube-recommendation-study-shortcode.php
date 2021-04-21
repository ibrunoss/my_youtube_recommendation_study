<?php 
if (! class_exists('My_Youtube_Recommendation_Study_Shortcode')) {
  class My_Youtube_Recommendation_Study_Shortcode
  {
    private $plugin_slug;

    public function __construct(string $slug)
    {
      $this->plugin_slug = $slug;
      add_shortcode('my-yt-rec-s', array($this, 'shortcode'));
    }

    public function shortcode($args)
    {
      extract($args);           

      $shortcode_unique_id = 'my_yt_rec_s_shortcode_' . wp_rand(1, 1000);

      // Check the widget options
      $limit    = (isset($limit) && $limit <= 15) ? $limit : 1;
      $layout   = (isset($layout) && $layout == 'list')  ? 'list' : 'grid';
      $language = get_locale();
      $content  = "
        <div id='$shortcode_unique_id'>" . __('Loading...', $this->plugin_slug) . "</div>
        <script>
        MyYoutubeRecommendationStudy.listCallbacks.push({
            container: '{$shortcode_unique_id}',
            layout: '{$layout}',
            limit: {$limit},
            lang: '{$language}',
            callback: MyYoutubeRecommendationStudy.buildList
        });
        </script>
      ";
      return $content;
    }//end:shortcode
  }//end:My_Youtube_Recommendation_Study_Shortcode
}