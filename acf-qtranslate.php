<?php
/*
Plugin Name: Advanced Custom Fields: qTranslate
Plugin URI: http://github.com/funkjedi/acf-qtranslate
Description: Provides multilingual versions of the text, text area, and wysiwyg fields.
Version: 1.7.22
Author: funkjedi
Author URI: http://funkjedi.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define('ACF_QTRANSLATE_PLUGIN',     __FILE__);
define('ACF_QTRANSLATE_PLUGIN_DIR', plugin_dir_path(ACF_QTRANSLATE_PLUGIN));

require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/plugin.php';
new acf_qtranslate_plugin;
