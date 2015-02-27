<?php

namespace acf_qtranslate\acf_4;

use acf_qtranslate\acf_4\fields\file;
use acf_qtranslate\acf_4\fields\image;
use acf_qtranslate\acf_4\fields\text;
use acf_qtranslate\acf_4\fields\textarea;
use acf_qtranslate\acf_4\fields\wysiwyg;
use acf_qtranslate\acf_interface;
use acf_qtranslate\plugin;

class acf implements acf_interface {

	/*
	 * Create an instance.
	 * @return void
	 */
	public function __construct() {
		$this->monkey_patch_qtranslate();

		add_filter('acf/format_value_for_api', array($this, 'format_value_for_api'));
		add_action('acf/register_fields',      array($this, 'register_fields'));
		add_action('admin_enqueue_scripts',    array($this, 'admin_enqueue_scripts'));
	}

	/**
	 * Load javascript and stylesheets on admin pages.
	 */
	public function register_fields() {
		new file;
		new image;
		new text;
		new textarea;
		new wysiwyg;
	}

	/**
	 * Load javascript and stylesheets on admin pages.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style('acf_qtranslate_input',  plugins_url('/assets/input.css', ACF_QTRANSLATE_PLUGIN));
		wp_enqueue_script('acf_qtranslate_input', plugins_url('/assets/input.js',  ACF_QTRANSLATE_PLUGIN));
	}

	/**
	 * This filter is applied to the $value after it is loaded from the db and
	 * before it is returned to the template via functions such as get_field().
	 */
	public function format_value_for_api($value) {
		if (is_string($value)) {
			$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
		}
		return $value;
	}

	/**
	 * Get the visible ACF fields.
	 * @return array
	 */
	public function get_visible_acf_fields() {
		global $post, $pagenow, $typenow;

		$filter = array();
		if ($pagenow === 'post.php' || $pagenow === 'post-new.php') {
			if ($typenow !== 'acf') {
				$filter['post_id'] = apply_filters('acf/get_post_id', false);
				$filter['post_type'] = $typenow;
			}
		}
		elseif ($pagenow === 'admin.php' && isset($_GET['page'])) {
			$filter['post_id'] = apply_filters('acf/get_post_id', false);
			$filter['post_type'] = $typenow;
		}
		elseif ($pagenow === 'edit-tags.php' && isset($_GET['taxonomy'])) {
			$filter['ef_taxonomy'] = filter_var($_GET['taxonomy'], FILTER_SANITIZE_STRING);
		}
		elseif ($pagenow === 'profile.php') {
			$filter['ef_user'] = get_current_user_id();
		}
		elseif ($pagenow === 'user-edit.php' && isset($_GET['user_id'])) {
			$filter['ef_user'] = filter_var($_GET['user_id'], FILTER_SANITIZE_NUMBER_INT);
		}
		elseif ($pagenow === 'user-new.php') {
			$filter['ef_user'] = 'all';
		}
		elseif ($pagenow === 'media.php' || $pagenow === 'upload.php') {
			$filter['post_type'] = 'attachment';
		}

		if (count($filter) === 0) {
			return array();
		}

		$supported_field_types = array(
			'email',
			'text',
			'textarea',
		);

		$visible_field_groups = apply_filters('acf/location/match_field_groups', array(), $filter);

		$visible_fields = array();
		foreach (apply_filters('acf/get_field_groups', array()) as $field_group) {
			if (in_array($field_group['id'], $visible_field_groups)) {
				$fields = apply_filters('acf/field_group/get_fields', array(), $field_group['id']);
				foreach ($fields as $field) {
					if (in_array($field['type'], $supported_field_types)) {
						$visible_fields[] = array('id' => $field['id']);
					}
				}
			}
		}

		return $visible_fields;
	}

	/**
	 * Monkey patches to fix little qTranslate javascript issues.
	 */
	public function monkey_patch_qtranslate() {
		global $q_config;

		// http://www.qianqin.de/qtranslate/forum/viewtopic.php?f=3&t=3497
		if (isset($q_config['js']) && strpos($q_config['js']['qtrans_switch'], 'originalSwitchEditors') === false) {
			$q_config['js']['qtrans_switch'] = "originalSwitchEditors = jQuery.extend(true, {}, switchEditors);\n" . $q_config['js']['qtrans_switch'];
			$q_config['js']['qtrans_switch'] = preg_replace("/(var vta = document\.getElementById\('qtrans_textarea_' \+ id\);)/", "\$1\nif(!vta)return originalSwitchEditors.go(id, lang);", $q_config['js']['qtrans_switch']);
		}

		// https://github.com/funkjedi/acf-qtranslate/issues/2#issuecomment-37612918
		if (isset($q_config['js']) && strpos($q_config['js']['qtrans_hook_on_tinyMCE'], 'ed.editorId.match(/^qtrans_/)') === false) {
			$q_config['js']['qtrans_hook_on_tinyMCE'] = preg_replace("/(qtrans_save\(switchEditors\.pre_wpautop\(o\.content\)\);)/", "if (ed.editorId.match(/^qtrans_/)) \$1", $q_config['js']['qtrans_hook_on_tinyMCE']);
		}
	}
}
