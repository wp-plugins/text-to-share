<?php
 /*
  Plugin Name: Text To Share
  Plugin URI: http://www.webdisrupt.com/wordpress-text-to-share-plugin
  Description: Configuration for plugin that allows a piece of text to be shared across social platforms.
  Version: 0.1
  Author: Fabio Zammit
  Author URI: http://rootcodex.com
  License: GNU General Public License v2
 */
 
// Check that WordPress was loaded before parsing
if (!defined('ABSPATH')) {
    die();
}

// Define the various constants so as to make it easy to update
define("TEXT_TO_SHARE",1);
define("TEXT_TO_SHARE_TAG","ttshare");
define("TEXT_TO_SHARE_TWITTER_URL","https://twitter.com/share?text=[*TEXT*]&url=[*URL*]");
define("TEXT_TO_SHARE_FACEBOOK_URL","https://www.facebook.com/sharer/sharer.php?u=[*URL*]");
define("TEXT_TO_SHARE_TWEET_TEXT","Share on Twitter");
define("TEXT_TO_SHARE_FB_TEXT","Share on Facebook");