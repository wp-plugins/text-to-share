<?php
 /*
  Plugin Name: Text To Share
  Plugin URI: http://www.webdisrupt.com/wordpress-text-to-share-plugin
  Description: Main class for plugin that allows a piece of text to be shared across social platforms.
  Version: 0.5.1
  Author: Fabio Zammit
  Author URI: http://rootcodex.com
  License: GNU General Public License v2
 */
 
// Check that config and WordPress was loaded before parsing
if (!defined('ABSPATH') || !defined("TEXT_TO_SHARE")) {
	die();
}

class Text_To_Share
{

	public static $instance = null;

	// We set the object to private so as to ensure this remains as a singleton
	private function __construct() {
		// Do nothing
	}

	// Method to create a singleton rather than a new object for each use
	public static function tts_get_instance() {
		if (self::$instance == null)
			self::$instance = new Text_To_Share();

		return self::$instance;
	}

	// Main method to initialise all actions, filters and shortcode
	public function tts_run() {
		add_action('init', array($this, 'tts_shortcode_button_init'));
		add_filter('query_vars', array($this, 'tts_add_trigger'));
		add_action('template_redirect', array($this, 'tts_get_assets'));
		add_shortcode(TEXT_TO_SHARE_TAG, array($this, 'tts_shortcode_filter'));
	}
	
	// Adds trigger for Wordpress so as to serve JavaScript 
	public function tts_add_trigger($vars) {
		$vars[] = 'tts_assets_trigger';
		return $vars;
	}

	// init process for registering our button
	public function tts_shortcode_button_init() {
		//Abort early if the user will never see TinyMCE
		if (!current_user_can('edit_posts') && !current_user_can('edit_pages') && get_user_option('rich_editing') == 'true')
			return;

		//Add a callback to regiser our tinymce plugin   
		add_filter("mce_external_plugins", array($this, 'tts_register_tinymce_plugin'));

		// Add a callback to add our button to the TinyMCE toolbar
		add_filter('mce_buttons', array($this, 'tts_add_tinymce_button'));
	}

	// This callback registers our plug-in
	public function tts_register_tinymce_plugin($plugin_array) {
		$plugin_array['tts_button'] = site_url() . '?tts_assets_trigger=1';	
		return $plugin_array;
	}

	// This callback adds our button to the toolbar
	public function tts_add_tinymce_button($buttons) {
		//Add the button ID to the $button array
		$buttons[] = "tts_button";
		return $buttons;
	}
	
	// Method to replace the special tags inside each URL
	private function tts_replace_url($content="", $url="") {
		return preg_replace(
					array('/\[\*TEXT\*\]/','/\[\*URL\*\]/'), 
					array($content, urlencode(get_permalink())), 
					$url);
	}
	
	// Filter for shortcode
	public function tts_shortcode_filter($atts, $content="") {	
		//Add CSS file to HEAD of page
		wp_enqueue_style( 'text-to-share-styles',TEXT_TO_SHARE_PATH.'css/main.css');
		
		//Prepare HTML to be replaced by filter
		$html_to_share = "";
		
		//Check if content has a string length of more than 1 and if so build the HTML
		if(strlen($content) > 1)
		{		
			$html_to_share = '<div class="text-to-share"><blockquote><p class="text-to-share-content">'.$content.'</p></blockquote>';
			$html_to_share .= '<div class="text-to-share-buttons"><a href="'.$this->tts_replace_url($content, TEXT_TO_SHARE_TWITTER_URL).'" title="Share on Twitter" class="text-to-share-twitter"><span>'.TEXT_TO_SHARE_TWEET_TEXT.'</span></a>';
			$html_to_share .= '<a href="'.$this->tts_replace_url($content, TEXT_TO_SHARE_FACEBOOK_URL).'" title="Share on Facebook" class="text-to-share-facebook"><span>'.TEXT_TO_SHARE_FB_TEXT.'</span></a>
			<p class="text-to-share-clear"></p>
			</div></div>';
		}
		
		return $html_to_share;
	}

	// Method to serve JavaScript once the query string is set
	public function tts_get_assets() {
		if(intval(get_query_var('tts_assets_trigger')) == 1) {
			$pathImage = TEXT_TO_SHARE_PATH;
			$tag = TEXT_TO_SHARE_TAG;
			header("Content-type: application/x-javascript;");

			echo <<<JAVASCRIPT
				jQuery(document).ready(function($) {
				tinymce.create('tinymce.plugins.tts_button', {
						init : function(ed, url) {
							// Register command for when button is clicked
							ed.addCommand('tts_button_insert_shortcode', function() {
								selected = tinyMCE.activeEditor.selection.getContent();

								if( selected ){
									//If text is selected when button is clicked then wrap shortcode around it.
									content =  '[{$tag}]'+selected+'[/{$tag}]';
									tinymce.execCommand('mceInsertContent', false, content);
								}
								else
									alert("You need to select a piece of text first");
							});

							// Register buttons - trigger above command when clicked
							ed.addButton('tts_button', {
								title : 'Text To Share', 
								cmd : 'tts_button_insert_shortcode', 
								image: '{$pathImage}img/icon.png'
							});
						},   
					});
					// Register our TinyMCE plugin
					tinymce.PluginManager.add('tts_button', tinymce.plugins.tts_button);
				});	
JAVASCRIPT;
			exit;
		}		
	}

}

