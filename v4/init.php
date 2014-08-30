<?php

add_action('admin_enqueue_scripts', 'acf_qtranslate_v4_admin_enqueue_scripts');
function acf_qtranslate_v4_admin_enqueue_scripts() {
	if (acf_qtranslate_acf_major_version() === 4) {
		wp_enqueue_style('acf_qtranslate_main', plugins_url('/assets/input.css', __FILE__));
		wp_enqueue_script('acf_qtranslate_main', plugins_url('/assets/input.js', __FILE__));
	}
}

add_action('acf/register_fields', 'acf_qtranslate_plugin_v4_register_fields');
function acf_qtranslate_plugin_v4_register_fields() {
	require_once dirname(__FILE__) . '/fields/text.php';
	require_once dirname(__FILE__) . '/fields/textarea.php';
	require_once dirname(__FILE__) . '/fields/wysiwyg.php';
	require_once dirname(__FILE__) . '/fields/image.php';
}

add_filter('acf/format_value_for_api', 'acf_qtranslate_plugin_v4_format_value_for_api');
function acf_qtranslate_plugin_v4_format_value_for_api($value) {
	if (acf_qtranslate_enabled() && is_string($value)) {
		$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
	}
	return $value;
}
