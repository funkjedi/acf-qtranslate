<?php
/*
Plugin Name: Advanced Custom Fields: qTranslate
Plugin URI: http://github.com/funkjedi/acf-qtranslate
Description: Provides multilingual versions of the text, text area, and wysiwyg fields.
Version: 1.7.8
Author: funkjedi
Author URI: http://funkjedi.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

define('ACF_QTRANSLATE_PLUGIN',     __FILE__);
define('ACF_QTRANSLATE_PLUGIN_DIR', plugin_dir_path(ACF_QTRANSLATE_PLUGIN));

require_once dirname(__FILE__) . '/v4/init.php';
require_once dirname(__FILE__) . '/v5/init.php';

function acf_qtranslate_enabled() {
	return function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage');
}

function acf_qtranslate_acf_major_version() {
	if (function_exists('acf')) {
		$acf = acf();
		return (int) $acf->settings['version'][0];
	}
	return '';
}



// qTranslate Monkey Patches
add_action('plugins_loaded', 'acf_qtranslate_monkey_patch', 3);
function acf_qtranslate_monkey_patch() {
	global $q_config;

	if (!array_key_exists('js', $q_config))
	{
		return;
	}

	// http://www.qianqin.de/qtranslate/forum/viewtopic.php?f=3&t=3497
	if (strpos($q_config['js']['qtrans_switch'], 'originalSwitchEditors') === false) {
		$q_config['js']['qtrans_switch'] = "originalSwitchEditors = jQuery.extend(true, {}, switchEditors);\n" . $q_config['js']['qtrans_switch'];
		$q_config['js']['qtrans_switch'] = preg_replace("/(var vta = document\.getElementById\('qtrans_textarea_' \+ id\);)/", "\$1\nif(!vta)return originalSwitchEditors.go(id, lang);", $q_config['js']['qtrans_switch']);
	}

	// https://github.com/funkjedi/acf-qtranslate/issues/2#issuecomment-37612918
	if (strpos($q_config['js']['qtrans_hook_on_tinyMCE'], 'ed.editorId.match(/^qtrans_/)') === false) {
		$q_config['js']['qtrans_hook_on_tinyMCE'] = preg_replace("/(qtrans_save\(switchEditors\.pre_wpautop\(o\.content\)\);)/", "if (ed.editorId.match(/^qtrans_/)) \$1", $q_config['js']['qtrans_hook_on_tinyMCE']);
	}
}
