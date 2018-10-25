<?php

class acf_qtranslate_acf_5_post_object extends acf_field_post_object {
	/**
	 * The plugin instance.
	 * @var \acf_qtranslate_plugin
	 */
	protected $plugin;

	function __construct($plugin) {
		$this->plugin = $plugin;

		if (version_compare($plugin->acf_version(), '5.6.0') < 0) {
			$this->initialize();
		}

		acf_field::__construct();
	}

	function initialize() {
		parent::initialize();
		$this->name = 'qtranslate_post_object';
		$this->category = 'qTranslate';
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
	 *  @date	10/07/18
	 */
	function render_field($field) {
		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = $this->get_values($field['value']);
		$currentLanguage = $this->plugin->get_active_language();

		// vars
		$o = array( 'type', 'id', 'class', 'name', 'value' );
		$s = array( 'readonly', 'disabled' );
		$e = '';

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

		echo '<div class="multi-language-field multi-language-field-post-object">';

		foreach ($languages as $language) {
			$class = 'wp-switch-editor';
			if ($language === $currentLanguage) {
				$class .= ' current-language';
			}
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language) {
			// Change Field into a select
			$field['type'] = 'select';
			$field['ui'] = 1;
			$field['ajax'] = 1;
			$field['choices'] = array();
			$field['name'] = $atts['name'] . "[$language]";
			$field['value'] = @$values[$language];

			$div = array(
				'class' => 'acf-post-object acf-cf',
				'data-language' => $language
			);

			if ($language === $currentLanguage) {
				$div['class'] .= ' current-language';
			} ?>

			<div <?php acf_esc_attr_e( $div ); ?>><?php
				// load posts
				$posts = $this->get_posts( $values[$language], $field );

				if( $posts ) {

					foreach( array_keys($posts) as $i ) {

						// vars
						$post = acf_extract_var( $posts, $i );


						// append to choices
						$field['choices'][ $post->ID ] = $this->get_post_title( $post, $field );

					}

				}

    			acf_render_field( $field ); ?>
			</div><?php
		}

		echo '</div>';
	}

	function get_values($value) {
		$languages = qtrans_getSortedLanguages(true);

		// Protection for field type change
		if (is_numeric($value)) {
			$value = array_reduce($languages, function($acc, $language) use ($value) {
				$acc[$language] = $value;
				return $acc;
			});
		}

		return $value;
	}

	function get_posts( $value, $field ) {

		// bail early if no value
		if( empty($value) ) return false;


		if (!is_array($value)) {
		    $value = [$value];
        }

		// get posts
		$posts = acf_get_posts(array(
			'post__in'	=> $value,
			'post_type'	=> $field['post_type']
		));


		// return
		return $posts;

	}


	function format_value( $value, $post_id, $field ) {
		$currentLanguage = $this->plugin->get_active_language();

		$value = $this->get_values($value);

		// bail early if no value
		if( empty($value) ) return false;


		// load posts if needed
		if( $field['return_format'] == 'object' ) {

			$value = $this->get_posts( $value[$currentLanguage], $field );

		}


		// convert back from array if neccessary
		if( !$field['multiple'] && acf_is_array($value) ) {

			$value = current($value);

		}


		// return value
		return $value;

	}

	function update_value( $value, $post_id, $field ) {

		// validate
		if( empty($value) ) {

			return $value;

		}

		$languages = qtrans_getSortedLanguages(true);

		foreach ($languages as $language) {
			// format
			if ( is_array( $value[$language] ) ) {

				// array
				foreach ( $value[$language] as $k => $v ) {

					// object?
					if ( is_object( $v ) && isset( $v->ID ) ) {

						$value[$language][ $k ] = $v->ID;

					}

				}


				// save value as strings, so we can clearly search for them in SQL LIKE statements
				$value[$language] = array_map( 'strval', $value[$language] );

			} elseif ( is_object( $value[$language] ) && isset( $value[$language]->ID ) ) {

				// object
				$value[$language] = $value[$language]->ID;

			}
		}


		// return
		return $value;

	}
}