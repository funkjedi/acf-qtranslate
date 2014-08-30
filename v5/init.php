<?php

add_action('acf/input/admin_enqueue_scripts', 'acf_qtranslate_v5_admin_enqueue_scripts');
function acf_qtranslate_v5_admin_enqueue_scripts() {
	if (acf_qtranslate_acf_major_version() === 5) {
		wp_enqueue_style('editor-buttons');
		wp_enqueue_style('acf_qtranslate_main', plugins_url('/assets/input.css', __FILE__));
		wp_enqueue_script('acf_qtranslate_main', plugins_url('/assets/input.js', __FILE__));
	}
}

add_action('acf/include_field_types', 'acf_qtranslate_plugin_v5_include_field_types');
function acf_qtranslate_plugin_v5_include_field_types($version) {
	require_once dirname(__FILE__) . '/fields/text.php';
	require_once dirname(__FILE__) . '/fields/textarea.php';
	require_once dirname(__FILE__) . '/fields/wysiwyg.php';
	require_once dirname(__FILE__) . '/fields/image.php';
}

add_filter('acf/format_value', 'acf_qtranslate_plugin_v5_format_value');
function acf_qtranslate_plugin_v5_format_value($value) {
	// we must check the version here since acf/format_value is a valid filter
	// ACF v4 however it serves a slightly different purpose
	if (acf_qtranslate_acf_major_version() === 5) {
		if (acf_qtranslate_enabled() && is_string($value)) {
			$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
		}
		return $value;
	}
	return $value;
}
