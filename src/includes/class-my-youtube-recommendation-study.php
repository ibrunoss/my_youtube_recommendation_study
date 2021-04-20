<?php
if (! class_exists('My_Youtube_Recommendation_Study')) {
  class My_Youtube_Recommendation_Study
  {
    public $options;
    private $plugin_slug;
    
    public function __construct(string $slug)
    {
      $this->options     = get_option('my_yt_rec_s');
      $this->plugin_slug = $slug;
      // Mandatory Info for Plugin Work
      if ($this->options['channel_id'] != "") {
        // Filters
        add_filter('the_content', array($this, 'add_videos_list_in_single_content'));

        // Actions
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
      }
    }//end:__construct
  
    public function add_videos_list_in_single_content($content)
    {
      if (is_single()) {  
        $position = $this->options['show_position'];
        if ($position == 'before') {
          $content =  $this->build_html_videos_list() . $content;
        } elseif ($position == 'after') {
          $content .=  $this->build_html_videos_list();
        }
        return $content;
      }
    }//end:add_videos_list_in_single_content

    private function build_html_videos_list(): string
    {
      $limit        = $this->options['limit'];
      $layout       = $this->options['layout'];
      $custom_css   = $this->options['custom_css'];
      $custom_css   = strip_tags($custom_css);
      $custom_css   = htmlspecialchars($custom_css, ENT_HTML5 | ENT_NOQUOTES | ENT_SUBSTITUTE, 'utf-8');
      $language     = get_locale();
      $container_id = 'my-yt-rec-s-container';
      $content      = '';

      if ($custom_css != "") {
        $content .= "<style>$custom_css</style>";
      }

      $content .= "<div id='$container_id'>".__('Loading...', $this->plugin_slug)."</div>";
      $script   = "
        <script>
          MyYoutubeRecommendationStudy.listCallbacks.push({
          container: '{$container_id}',
          layout: '{$layout}',
          limit: {$limit},
          lang: '{$language}',
          callback: MyYoutubeRecommendationStudy.buildList
          });
        </script>
      ";
      return $content . $script;
    }//end:build_html_videos_list

    public function enqueue_assets()
    {
      $slug = $this->plugin_slug;

      wp_enqueue_style("{$slug}-style", plugin_dir_url(__DIR__) . 'public/css/style.css');
      wp_enqueue_script("{$slug}-script", plugin_dir_url(__DIR__) . 'public/js/script.js', array('jquery'), '', false);
      wp_enqueue_script("{$slug}-loader", plugin_dir_url(__DIR__) . 'public/js/loader.js', array('jquery', "{$slug}-script"), '', true);

      // Creates the variable my_yt_rec_s_ajax and assigns it an object with a key: url and value: the address up to the admin-ajax.php file
      wp_localize_script("{$slug}-script", 'my_yt_rec_s_ajax', array('url' => network_admin_url('admin-ajax.php')));
    }//end:enqueue_assets
  }//end:My_Youtube_Recommendation_Study
}