<?php
/*
  Plugin Name: Text To Share
  Plugin URI: http://www.webdisrupt.com/wordpress-text-to-share-plugin
  Description: A plugin that allows a piece of text to be shared across social platforms.
  Version: 0.5.1
  Author: Fabio Zammit
  Author URI: http://rootcodex.com
  License: GNU General Public License v2
 */

// Check that WordPress was loaded before parsing 
if (!defined('ABSPATH')) {
	die();
}

// Check that Config was loaded before requiring the various PHP files
if (!defined('TEXT_TO_SHARE')) {
	
	/**
	 * Require plugin configuration
	 */	
	
	define("TEXT_TO_SHARE_PATH",plugin_dir_url(__FILE__));
	
	require_once dirname(__FILE__) . '/inc/config.php';
	require_once dirname(__FILE__) . '/inc/text_to_share.class.php';

	/**
	 * Run plugin
	 */
	$tts_plugin =Text_To_Share::tts_get_instance();	
	$tts_plugin->tts_run();
};


