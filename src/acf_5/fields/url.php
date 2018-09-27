<?php

class acf_qtranslate_acf_5_url extends acf_qtranslate_acf_5_text {
	function initialize() {

		// vars
		$this->name = 'qtranslate_url';
		$this->label = __("Url",'acf');
		$this->category = __("qTranslate",'acf');
		$this->defaults = array(
			'default_value'	=> '',
			'maxlength'		=> '',
			'placeholder'	=> '',
			'prepend'		=> '',
			'append'		=> ''
		);

	}

	/*
	 *  render_field()
	 *
	 *  Create the HTML interface for your field
	 *
	 *  @param	$field - an array holding all the field's data
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	function render_field($field) {
		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);
		$currentLanguage = $this->plugin->get_active_language();

		// vars
		$o = array( 'type', 'id', 'class', 'name', 'value', 'placeholder' );
		$s = array( 'readonly', 'disabled' );
		$e = '';

		// maxlength
		if( $field['maxlength'] !== "" ) {
			$o[] = 'maxlength';
		}

		// populate atts
		$atts = array();
		foreach( $o as $k ) {
			$atts[ $k ] = $field[ $k ];
		}

		// special atts
		foreach( $s as $k ) {
			if( isset($field[ $k ]) && $field[ $k ] ) {
				$atts[ $k ] = $k;
			}
		}

		// render
		$e .= '<div class="acf-url-wrap multi-language-field">';

		foreach ($languages as $language) {
			$class = ($language === $currentLanguage) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			$e .= '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language) {
			$atts['class'] = $field['class'];

			$atts['type'] = 'text';
			$atts['name'] = $field['name'] . "[$language]";
			$atts['value'] = $values[$language];

			$container_class = 'acf-input-wrap acf-url';
			if ($language === $currentLanguage) {
				$container_class .= ' current-language';
			}

			$e .= '<div class="' . $container_class . '" data-language="' . $language . '">';
			$e .= '<i class="acf-icon -globe -small"></i>';
			$e .= '<input ' . acf_esc_attr( $atts ) . ' />';
			$e .= '</div>';

		}

		$e .= '</div>';

		// return
		echo $e;
	}

	function validate_value( $valid, $value, $field, $input ){
		foreach ($value as $valor) {
			// bail early if empty
			if ( empty( $valor ) ) {

				continue;

			}

			if ( strpos( $valor, '://' ) !== false ) {

				// url

			} elseif ( strpos( $valor, '//' ) === 0 ) {

				// protocol relative url

			} elseif ( strpos( $valor, '/' ) === 0 ) {

				// relative url

			} else {

				$valid = __( 'Value must be a valid URL', 'acf' );

			}
		}

		// return
		return $valid;
	}
}
