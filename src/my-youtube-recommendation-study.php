<?php
/**
 * @link              https://github.com/ibrunoss/my_youtube_recommendation_study
 * @since             1.0.0
 * @package           My_Youtube_Recommendation_Study
 * 
 * @wordpress-plugin
 * Plugin Name:       My Youtube Recommendation Study
 * Plugin URI:        https://github.com/ibrunoss/my_youtube_recommendation_study
 * Description:       Display the last videos from a Youtube channel using Youtube feed and keep always updated even for cached posts. This plugin was built through the course: "DESENVOLVIMENTO DE PLUGINS PARA WORDPRESS" on the HostGator platform (https://app.collabplay.online) 
 * Version:           1.0.0
 * Author:            Bruno Silva Santana
 * Author URI:        https://github.com/ibrunoss
 * License:           GPLv3 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       my-youtube-recommendation-study
 * Domain Path:       /languages/
 */

 // If this file is called directly, abort.
 if (! defined('WPINC')) {
	wp_die();
}

// Plugin Version
if (! defined('MY_YOUTUBE_RECOMMENDATION_STUDY_VERSION')) {
    define('MY_YOUTUBE_RECOMMENDATION_STUDY_VERSION', '1.0.0');
}

// Plugin Name
if (! defined('MY_YOUTUBE_RECOMMENDATION_STUDY_NAME')) {
    define('MY_YOUTUBE_RECOMMENDATION_STUDY_NAME', 'My Youtube Recommendation Study');
}

// Plugin Slug
if (! defined('MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG')) {
	define('MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG', 'my-youtube-recommendation-study');
}

// Plugin Basename
if (! defined('MY_YOUTUBE_RECOMMENDATION_STUDY_BASENAME')) {
	define('MY_YOUTUBE_RECOMMENDATION_STUDY_BASENAME', plugin_basename(__FILE__));
}

// Plugin Folder
if (! defined('MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR')) {
	define('MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR', plugin_dir_path(__FILE__));
}

// JSON File Name
if (! defined('MY_YOUTUBE_RECOMMENDATION_STUDY_JSON_FILENAME')) {
	define('MY_YOUTUBE_RECOMMENDATION_STUDY_JSON_FILENAME', 'my-yt-rec-s.json');
}

// Dependencies
require_once MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR . 'includes/class-my-youtube-recommendation-study.php';
require_once MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR . 'includes/class-my-youtube-recommendation-study-json.php';
require_once MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR . 'includes/class-my-youtube-recommendation-study-widget.php';
require_once MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR . 'includes/class-my-youtube-recommendation-study-shortcode.php';

$my_youtube_recommendation_study = new My_Youtube_Recommendation_Study(MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG);

if ($my_youtube_recommendation_study->options['channel_id'] != '') {
	$my_youtube_recommendation_study_json = new My_Youtube_Recommendation_Study_Json (
		$my_youtube_recommendation_study->options['channel_id'],
		(int)$my_youtube_recommendation_study->optionss['cache_expiration'],
		MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG,
		MY_YOUTUBE_RECOMMENDATION_STUDY_JSON_FILENAME
	);
}

$my_youtube_recommendation_study_shortcode = new My_Youtube_Recommendation_Study_Shortcode(MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG);
$my_youtube_recommendation_study_widget = new My_Youtube_Recommendation_Study_Widget(MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG);

if (is_admin()) {
	require_once MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_DIR . 'includes/class-my-youtube-recommendation-study-admin.php';
	$my_youtube_recommendation_study_admin = new My_Youtube_Recommendation_Study_Admin(
		MY_YOUTUBE_RECOMMENDATION_STUDY_NAME,
		MY_YOUTUBE_RECOMMENDATION_STUDY_BASENAME,
		MY_YOUTUBE_RECOMMENDATION_STUDY_PLUGIN_SLUG,
		MY_YOUTUBE_RECOMMENDATION_STUDY_JSON_FILENAME,
		MY_YOUTUBE_RECOMMENDATION_STUDY_VERSION
	);
}