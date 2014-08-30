<?php

class acf_field_qtranslate_textarea extends acf_field_textarea
{

	function __construct()
	{
		$this->name = 'qtranslate_textarea';
		$this->label = __("Text Area",'acf');
		$this->category = __("qTranslate",'acf');
		$this->defaults = array(
			'default_value'	=> '',
			'new_lines'		=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'readonly'		=> 0,
			'disabled'		=> 0,
			'rows'			=> ''
		);

		acf_field::__construct();
	}


	function render_field($field)
	{
		if (!acf_qtranslate_enabled()) {
			acf_field_textarea::render_field($field);
			return;
		}

		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);


		// vars
		$o = array( 'id', 'class', 'name', 'placeholder', 'rows' );
		$s = array( 'readonly', 'disabled' );
		$e = '';


		// maxlength
		if( $field['maxlength'] !== '' ) {

			$o[] = 'maxlength';

		}


		// rows
		if( empty($field['rows']) ) {

			$field['rows'] = 8;

		}


		// populate atts
		$atts = array();
		foreach( $o as $k ) {

			$atts[ $k ] = $field[ $k ];

		}


		// special atts
		foreach( $s as $k ) {

			if( $field[ $k ] ) {

				$atts[ $k ] = $k;

			}

		}


		// render
		$e .= '<div class="acf-input-wrap multi-language-field">';

		foreach ($languages as $language) {
			$class = ($language === end($languages)) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			$e .= '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language) {
			if ($language === end($languages)) {
				$atts['class'] .= ' current-language';
			}
			$atts['name'] = $field['name'] . "[$language]";
			$atts['data-language'] = $language;
			$e .= '<textarea ' . acf_esc_attr( $atts ) . ' >';
			$e .= esc_textarea( $values[$language] );
			$e .= '</textarea>';
		}

		$e .= '</div>';


		// return
		echo $e;
	}


	function update_value($value, $post_id, $field)
	{
		if (acf_qtranslate_enabled()) {
			$value = qtrans_join($value);
		}

		return $value;
	}

}


new acf_field_qtranslate_textarea;
