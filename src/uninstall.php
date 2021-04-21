<?php
if (! defined('ABSPATH' ) && ! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}

delete_option('my_yt_rec_s');
		
$upload_dir  = wp_upload_dir();
$json_folder = $upload_dir['basedir'] . '/my-youtube-recommendation-study' ;
$json_file 	 = $json_folder . '/my-yt-rec-s.json';

unlink($json_file);
rmdir($json_folder);