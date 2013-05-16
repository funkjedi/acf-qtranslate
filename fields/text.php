<?php

class acf_field_qtranslate_text extends acf_field_text
{

	function __construct()
	{
		$this->name = 'text';
		$this->label = __("Text",'acf');

		// remove registered core filters and actions for text field
		remove_all_filters('acf/load_value/type=' . $this->name);
		remove_all_filters('acf/update_value/type=' . $this->name);
		remove_all_filters('acf/format_value/type=' . $this->name);
		remove_all_filters('acf/format_value_for_api/type=' . $this->name);
		remove_all_filters('acf/load_field/type=' . $this->name);
		remove_all_filters('acf/update_field/type=' . $this->name);
		remove_all_actions('acf/create_field/type=' . $this->name);
		remove_all_actions('acf/create_field_options/type=' . $this->name);

		acf_field::__construct();
	}


	function create_field($field)
	{
		if (!acf_qtranslate_enabled()) {
			acf_field_text::create_field($field);
			return;
		}

		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);

		echo '<div class="multi-language-field">';

		foreach ($languages as $language) {
			$class = ($language === end($languages)) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language) {
			$class = ($language === end($languages)) ? $field['class'] . ' current-language' : $field['class'];
			echo '<input type="text" data-language="' . esc_attr($language) . '" value="' . esc_attr($values[$language]) . '" id="' . esc_attr( $field['id'] ) . '" class="' . esc_attr($class) . '" name="' . esc_attr($field['name'] . "[$language]") . '" />';
		}

		echo '</div>';
	}


	function format_value($value, $post_id, $field)
	{
		if (!acf_qtranslate_enabled()) {
			return acf_field_text::format_value($value, $post_id, $field);
		}

		return $value;
	}


	function format_value_for_api($value, $post_id, $field) {
		if (acf_qtranslate_enabled()) {
			$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
		}

		return acf_field_text::format_value_for_api($value, $post_id, $field);
	}


	function update_value($value, $post_id, $field)
	{
		if (acf_qtranslate_enabled()) {
			$value = qtrans_join($value);
		}

		return $value;
	}

}


new acf_field_qtranslate_text;
