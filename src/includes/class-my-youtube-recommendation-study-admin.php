<?php

if (! class_exists('My_Youtube_Recommendation_Study_Admin')) {
  class My_Youtube_Recommendation_Study_Admin
  {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $plugin_name;
    private $plugin_basename;
    private $plugin_slug;
    private $plugin_version;
    private $json_filename;

    public function __construct(
      string $name, 
      string $basename, 
      string $slug, 
      string $json_filename, 
      string $version
    ) {
        $this->options         = get_option('my_yt_rec_s');
        $this->plugin_name     = $name;
        $this->plugin_basename = $basename;
        $this->plugin_slug     = $slug;
        $this->plugin_version  = $version;
        $this->json_filename   = $json_filename;

        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_init', array($this, 'page_init'));
        add_action('admin_footer_text', array($this, 'page_footer'));
        add_action('admin_notices', array($this, 'show_notices'));

        add_filter('plugin_action_links_' . $this->plugin_basename, array($this, 'add_settings_link'));
    }
    
    /**
     * Add options page
     */
    public function add_plugin_page()
    {
      // This page will be under "Settings"
      add_options_page(
        __('Settings', $this->plugin_slug), 
        $this->plugin_name, 
        'manage_options', 
        $this->plugin_slug, 
        array($this, 'create_admin_page')
      );
    } // end:add_plugin_page

    /**
     * Add settings link on plugins page
     */
    public function add_settings_link(array $links): array
    {
      $settings_link = '<a href="options-general.php?page='. $this->plugin_slug .'">' . __('Settings', $this->plugin_slug) . '</a>';
      array_unshift($links, $settings_link);
      return $links;
    } // end:add_settings_link

    /**
     * Show notices on admin dashboard
     */
    public function show_notices()
    {
      $value = isset($this->options['channel_id']) ? esc_attr($this->options['channel_id']) : '';
      if ($value == '') {
          ?>
          <div class="error notice">
          <?php echo $channel_id ?>
              <p><strong><?php echo __('My Youtube Recommendation', $this->plugin_slug); ?></strong></p>
              <p><?php echo __('Fill with your Youtube channel ID', $this->plugin_slug); ?></p>
          </div>
          <?php
      }
    } // end:show_notices
  
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
      ?>
      <div class="wrap">
          <h1><?php echo $this->plugin_name; ?></h1>
          <form method="post" action="options.php">
          <?php
              // This prints out all hidden setting fields
              settings_fields('my_yt_rec_s_options');
              do_settings_sections('my-yt-rec-s-admin');
              submit_button();
          ?>
          </form>
      </div>
      <?php
    } // end:create_admin_page
    
    /**
     * Register and add settings
     */
    public function page_init()
    {   
      $page = 'my-yt-rec-s-admin';
      $sid  = array( 
        'setting_section_id_1',
        'setting_section_id_2',
        'setting_section_id_3',
      ); // Section ID

      register_setting(
          'my_yt_rec_s_options', // Option group
          'my_yt_rec_s', // Option name
          array($this, 'sanitize') // Sanitize
      );

      add_settings_section(
          $sid[0], // ID
          __('General Settings', $this->plugin_slug), // Title
          null, // Callback
          $page // Page
      );  

      add_settings_field(
          'channel_id', // ID
          __('Channel Id', $this->plugin_slug), // Title 
          array($this, 'channel_id_callback'), // Callback
          $page, // Page
          $sid[0]// Section           
      );   

      add_settings_field(
          'cache_expiration',
          __('Cache Expiration', $this->plugin_slug), 
          array($this, 'cache_expiration_callback'), 
          $page, 
          $sid[0] 
      );  

      add_settings_section(
          $sid[1],
          __('Post Settings', $this->plugin_slug),
          null,
          $page
      );    

      add_settings_field(
          'show_position', 
          __('Show in Posts', $this->plugin_slug), 
          array($this, 'show_position_callback'), 
          $page, 
          $sid[1]
      );  
      
      add_settings_field(
          'layout', 
          __('Layout', $this->plugin_slug), 
          array($this, 'show_layout_callback'), 
          $page, 
          $sid[1]
      );  

      add_settings_field(
          'limit',
          __('Videos in List', $this->plugin_slug),
          array($this, 'limit_callback'),
          $page,
          $sid[1]
      );  

      add_settings_section(
          $sid[2],
          __('Customize Style', $this->plugin_slug), 
          null, 
          $page
      );  

      add_settings_field(
          'custom_css', 
          __('Your CSS', $this->plugin_slug), 
          array($this, 'custom_css_callback'), 
          $page, 
          $sid[2]
      );  
    } // end:page_init

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     * @return array $new_input Contains all settings fields as array keys and their sanitized data
     */
    public function sanitize(array $input): array
    {

      $new_input = array();          

      if (isset($input['channel_id'])) {
        $new_input['channel_id'] = sanitize_text_field($input['channel_id']);
      }

      if (isset($input['cache_expiration'])) {
        $new_input['cache_expiration'] = absint($input['cache_expiration']);
      }

      if (isset($input['show_position'])) {
        $new_input['show_position'] = sanitize_text_field($input['show_position']);
      }

      if (isset($input['layout'])) {
        $new_input['layout'] = sanitize_text_field($input['layout']);
      }

      if (isset($input['limit'])) {
        $new_input['limit'] = absint($input['limit']);
      }

      if (isset($input['custom_css'])) {
        $new_input['custom_css'] = sanitize_text_field($input['custom_css']);
      }

      return $new_input;
    } // end:sanitize
    
    /** 
     * Get the settings option array and print one of its values
     */
    public function channel_id_callback() 
    {
      $value = isset($this->options['channel_id']) ? esc_attr($this->options['channel_id']) : '';
      ?>
      <input type="text" id="channel_id" name="my_yt_rec_s[channel_id]" value="<?php echo $value ?>" class="regular-text" />
          <p class="description"><?php echo __('sample', $this->plugin_slug) ?>: UCFuIUoyHB12qpYa8Jpxoxow</p>
          <p class="description"><a href="https://support.google.com/youtube/answer/3250431" target="_blank"><?php echo __('Find here your channel Id', $this->plugin_slug) ?></a></p>
      <?php
    } // end:channel_id_callback

    public function cache_expiration_callback()
    {
      $upload_dir = wp_upload_dir();
      $json_url   = $upload_dir['baseurl'] . '/' . $this->plugin_slug . '/' . $this->json_filename;
      $value      = isset($this->options['cache_expiration']) ? esc_attr($this->options['cache_expiration']) : '1';

      ?>
          <input type="number" id="cache_expiration" min="1" name="my_yt_rec_s[cache_expiration]" value="<?php echo $value ?>" class="small-text" />
          <?php echo __('hours is the expiration time for cached data', $this->plugin_slug) ?>.
          <p class="description"><a href="<?php echo $json_url?>" target="_blank"><?php echo __('Test here', $this->plugin_slug) ?></a>.
      <?php
    } // end:cache_expiration_callback

    public function show_position_callback()
    {
      $value = isset($this->options['show_position']) ? esc_attr($this->options['show_position']) : '';

      ?>
      <fieldset>
          <legend class="screen-reader-text"><span><?php echo __('On posts show videos in position:', $this->plugin_slug) ?></span></legend>
          <label><input type="radio" name="my_yt_rec_s[show_position]" value=""<?php echo ($value == '') ? 'checked="checked"' : '' ?>> <?php echo __('Disable', $this->plugin_slug) ?></label><br>
          <label><input type="radio" name="my_yt_rec_s[show_position]" value="after"<?php echo ($value == 'after') ? 'checked="checked"' : '' ?>> <?php echo __('After', $this->plugin_slug) ?></label><br>
          <label><input type="radio" name="my_yt_rec_s[show_position]" value="before"<?php echo ($value == 'before') ? 'checked="checked"' : '' ?>> <?php echo __('Before', $this->plugin_slug) ?></label>
      </fieldset>
      <?php
    } // end:show_position_callback

    public function show_layout_callback()
    {
      $value = isset($this->options['layout']) ? esc_attr($this->options['layout']) : 'grid';
      ?>
      <select name="my_yt_rec_s[layout]">
          <option value="grid"<?php echo ($value == 'grid') ? 'selected="selected"' : '' ?>><?php echo __('Grid', $this->plugin_slug) ?></option>
          <option value="list"<?php echo ($value == 'list') ? 'selected="selected"' : '' ?>><?php echo __('List', $this->plugin_slug) ?></option>
      </select>
      <?php
    } // end:show_layout_callback

    public function limit_callback()
    {
      $value = isset($this->options['limit']) ? esc_attr($this->options['limit']) : '3';
      ?>
      <input type="number" id="limit" min="1" max="15" name="my_yt_rec_s[limit]" value="<?php echo $value ?>" class="small-text" />
      <p class="description"><?php echo __('Max', $this->plugin_slug) ?> 15</p>
      <?php
    } // end:limit_callback

    public function custom_css_callback()
    {
      $value = isset($this->options['custom_css']) ? esc_attr($this->options['custom_css']) : '';
      ?>
      <textarea id="custom_css" name="my_yt_rec_s[custom_css]" rows="10" cols="50" class="large-text code"><?php echo $value ?></textarea>
      <?php
    } // end:custom_css_callback

    public function page_footer()
    {
        return __('Plugin Version', $this->plugin_slug) . ' ' . $this->plugin_version;
    }
  } // end:My_Youtube_Recommendation_Study
}