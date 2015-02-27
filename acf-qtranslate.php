<?php
/*
Plugin Name: Advanced Custom Fields: qTranslate
Plugin URI: http://github.com/funkjedi/acf-qtranslate
Description: Provides multilingual versions of the text, text area, and wysiwyg fields.
Version: 1.7
Author: funkjedi
Author URI: http://funkjedi.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define('ACF_QTRANSLATE_PLUGIN',     __FILE__);
define('ACF_QTRANSLATE_PLUGIN_DIR', plugin_dir_path(ACF_QTRANSLATE_PLUGIN));


/**
 * Create autoload to load plugin classes.
 */
function acf_qtranslate_autoload($className) {
	if (strpos($className, 'acf_qtranslate\\') === 0) {
		$path = ACF_QTRANSLATE_PLUGIN_DIR . 'src/' . str_replace('\\', '/', substr($className, 15)) . '.php';
		if (is_readable($path)) {
			include $path;
		}
	}
}

spl_autoload_register('acf_qtranslate_autoload');




// bootstrap plugin
new acf_qtranslate\plugin;
