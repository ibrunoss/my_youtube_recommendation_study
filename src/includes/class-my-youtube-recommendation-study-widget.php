<?php 
if (! class_exists( 'My_Youtube_Recommendation_Study_Widget')) {
  class My_Youtube_Recommendation_Study_Widget extends WP_Widget
  {
    private static $plugin_slug;
    
    public function __construct()
    {
      parent::__construct(
        'my_youtube_recommendation_study_widget',
        __('My Youtube Recommendation Study', $this->plugin_slug),
        array('customize_selective_refresh' => true)
      );

      $args = func_get_args(); 

      if (isset($args[0])) {
        $this->plugin_slug = $args[0];
      }

      add_action('widgets_init', array($this, 'init'));
    }

    public function init()
    {
      register_widget('My_Youtube_Recommendation_Study_Widget');
    }

    // The widget form (for the backend )
    public function form($instance)
    {	
      // Set widget defaults
      $defaults = array(
        'title'  => __('Last Videos', $this->plugin_slug),
        'layout' => 'grid',
        'limit'  => '3'
      );
      
      // Parse current settings with defaults
      extract(wp_parse_args((array) $instance, $defaults));

      // Title ?>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php echo __('Title', $this->plugin_slug); ?>:</label>
        <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
      </p>

      <?php // Layout ?>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('layout')); ?>"><?php echo __('Layout', $this->plugin_slug); ?>:</label>
        <select class="postform" id="<?php echo esc_attr($this->get_field_id('layout')); ?>" name="<?php echo esc_attr($this->get_field_name('layout')); ?>">
        <option class="level-0" value="grid" <?php echo (esc_attr($layout) == 'grid') ? 'selected="selected"': '' ?>><?php echo __('Grid', $this->plugin_slug) ?></option>
        <option class="level-0" value="list" <?php echo (esc_attr($layout) == 'list') ? 'selected="selected"': '' ?>><?php echo __('List', $this->plugin_slug) ?></option>
        </select>
      </p>

      <?php // Limit ?>
      <p>
        <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php echo __('Videos to show', $this->plugin_slug); ?>:</label>
        <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" step="1" min="1" max="15" value="<?php echo esc_attr($limit); ?>" size="3" /> (15 <?php echo __('max', $this->plugin_slug) ?>)
      </p>

      <?php
    }//end:form

    // Update widget settings
    public function update($new_instance, $old_instance)
    {
      $instance           = $old_instance;
      $instance['title']  = isset($new_instance['title']) ? wp_strip_all_tags($new_instance['title']) : '';
      $instance['layout'] = isset($new_instance['layout']) ? wp_strip_all_tags($new_instance['layout']) : 'grid';
      $instance['limit']  = isset($new_instance['limit']) ? wp_strip_all_tags( $new_instance['limit']) : '';
      return $instance;
    }//end:update

    // Display the widget
    public function widget($args, $instance)
    {
      extract($args);

      $widget_unique_id = 'my_yt_rec_s_widget_' . wp_rand(1, 1000);

      // Check the widget options
      $title    = isset($instance['title']) ? apply_filters('title', $instance['title']) : '';
      $layout   = isset($instance['layout']) ? apply_filters('layout', $instance['layout']) : 'grid';
      $limit    = isset($instance['limit']) ? apply_filters('limit', $instance['limit']) : '';
      $language = get_locale();

      // WordPress core before_widget hook (always include)
      echo $before_widget;
      ?>
      <div class="widget-text wp_widget_plugin_box">
        <?php echo ($title) ? $before_title . $title . $after_title : ''; ?>
        <div id='<?php echo $widget_unique_id ?>'>
          <?php echo __('Loading...', $this->plugin_slug) ?>
        </div>
      </div>
      <script>
          MyYoutubeRecommendationStudy.listCallbacks.push({
          container: '<?php echo $widget_unique_id ?>',
          layout: '<?php echo $layout ?>',
          limit: <?php echo $limit ?>,
          lang: '<?php echo $language ?>',
          callback: MyYoutubeRecommendationStudy.buildList
          });
      </script>
      <?php
      // WordPress core after_widget hook (always include )
      echo $after_widget;
    }//end:widget
  }//end:My_Youtube_Recommendation_Widget
}