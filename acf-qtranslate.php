<?php
/*
Plugin Name: Advanced Custom Fields: qTranslate
Plugin URI: http://github.com/funkjedi/acf-qtranslate
Description: Provides multilingual versions of the text, text area, and wysiwyg fields.
Version: 1.2.0
Author: funkjedi
Author URI: http://funkjedi.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// http://support.advancedcustomfields.com/discussion/1181/prease-check-wp3-3-qtranslate-advance-custom-field/p1


function acf_qtranslate_enabled() {
	return function_exists('qtrans_getSortedLanguages');
}

add_action('acf/register_fields', 'acf_qtranslate_plugin_register_fields');
function acf_qtranslate_plugin_register_fields() {

	require_once dirname(__FILE__) . '/fields/text.php';
	require_once dirname(__FILE__) . '/fields/textarea.php';
	require_once dirname(__FILE__) . '/fields/wysiwyg.php';
	require_once dirname(__FILE__) . '/fields/image.php';

}

add_action('admin_enqueue_scripts', 'acf_qtranslate_admin_enqueue_scripts');
function acf_qtranslate_admin_enqueue_scripts() {
	wp_enqueue_style('acf_qtranslate_main', plugins_url('/assets/main.css', __FILE__));
	wp_enqueue_script('acf_qtranslate_main', plugins_url('/assets/main.js', __FILE__));
}



// enable qtranslate quicktags for all string based fields
add_filter('acf/format_value_for_api', 'acf_qtranslate_plugin_format_value_for_api');
function acf_qtranslate_plugin_format_value_for_api($value) {
	if (acf_qtranslate_enabled() && is_string($value)) {
		$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
	}

	return $value;
}


add_action('plugins_loaded', 'acf_qtranslate_monkey_patch', 3);
function acf_qtranslate_monkey_patch() {
	global $q_config;

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
