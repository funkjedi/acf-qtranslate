<?php
/*
Plugin Name: Advanced Custom Fields: qTranslate
Plugin URI: http://github.com/funkjedi/acf-qtranslate
Description: Provides multilingual versions of the text, text area, and wysiwyg fields.
Version: 1.4
Author: funkjedi
Author URI: http://funkjedi.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
*/

// http://support.advancedcustomfields.com/discussion/1181/prease-check-wp3-3-qtranslate-advance-custom-field/p1

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


// if using qTranslate-X include a qTranslate compatibility
// layer for all required functions
add_action('plugins_loaded', 'acf_qtranslate_compatibility', 3);
function acf_qtranslate_compatibility() {
	if (defined('QTX_VERSION')) {
		require_once dirname(__FILE__) . '/qtranslate-compatibility.php';
	}
}


// qTranslate Monkey Patches
add_action('plugins_loaded', 'acf_qtranslate_monkey_patches', 3);
function acf_qtranslate_monkey_patches() {
	global $q_config;

	// qTranslate and mqTranslate init at priority 3 so if they
	// are active then QTRANS_INIT should be defined
	if (acf_qtranslate_enabled() && defined('QTRANS_INIT')) {

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
}


function acf_qtranslate_get_visible_fields($args = false) {
	global $post, $typenow;
	if ($args === false) {
		$args = array(
			'post_id'	=> $post->ID, 
			'post_type'	=> $typenow,
		);
	}
	$supported_field_types = array(
		'text',
		'textarea',
	);
	$field_ids = array();
	foreach (acf_get_field_groups($args) as $field_group) {
		$fields = acf_get_fields($field_group);
		foreach ($fields as $field) {
			if (in_array($field['type'], $supported_field_types)) {
				$field_ids[] = array('id' => 'acf-' . $field['key']);
			}
		}
	}
	return $field_ids;
}


add_filter('qtranslate_load_admin_page_config', 'acf_qtranslate_load_admin_page_config');
function acf_qtranslate_load_admin_page_config($page_configs)
{
	global $pagenow;

	// very hacky support for v4
	if (acf_qtranslate_acf_major_version() === 4) {
		$args = array('post_id' => apply_filters('acf/get_post_id', false));
		$match_field_groups = apply_filters('acf/location/match_field_groups', array(), $args);
		$acfs = apply_filters('acf/get_field_groups', array());
		if (is_array($acfs)) {
			$field_ids = array();
			foreach ($acfs as $acf) {
				if (in_array($acf['id'], $match_field_groups)) {
					$fields = apply_filters('acf/field_group/get_fields', array(), $acf['id']);
					foreach ($fields as $field) {
						$field_ids[] = array('id' => $field['id']);
					}
				}
			}
			if (count($field_ids)) {
				$page_configs[] = array(
					'pages' => array('' => ''), 
					'forms' => array(
						array('fields' => $field_ids)
					));
			}
		}
		return $page_configs;
	}


	switch ($pagenow) {

		// add support for regular pages and posts
		case 'post.php':
		case 'post-new.php':
			$page_configs[] = array(
				'pages' => array(
					'post.php'     => '', 
					'post-new.php' => ''), 
				'forms' => array(
					array('fields' => acf_qtranslate_get_visible_fields())
				));
			break;

		// add support for ACF Option Pages
		case 'admin.php':
			foreach (acf_get_options_pages() as $page) {
				$page_configs[] = array(
					'pages' => array('admin.php' => 'page=' . $page['menu_slug']), 
					'forms' => array(
						array('fields' => acf_qtranslate_get_visible_fields())
					));
			}
			break;

		// add support for new user page
		case 'user-new.php':
			$args = array(
				'user_id'   => 'new',
				'user_form' => 'edit',
			);
			$page_configs[] = array(
				'pages' => array('user-new.php'  => ''), 
				'forms' => array(
					array('fields' => acf_qtranslate_get_visible_fields($args))
				));
			break;

		// add support for edit user page
		case 'user-edit.php':
			$args = array(
				'user_id'   => @$_GET['user_id'],
				'user_form' => 'edit',
			);
			$page_configs[] = array(
				'pages' => array('user-edit.php' => 'user_id='), 
				'forms' => array(
					array('fields' => acf_qtranslate_get_visible_fields($args))
				));
			break;

		// add support for profile page
		case 'profile.php':
			$args = array(
				'user_id'   => get_current_user_id(),
				'user_form' => 'edit',
			);
			$page_configs[] = array(
				'pages' => array('profile.php' => ''), 
				'forms' => array(
					array('fields' => acf_qtranslate_get_visible_fields($args))
				));
			break;

	}

	return $page_configs;
}
