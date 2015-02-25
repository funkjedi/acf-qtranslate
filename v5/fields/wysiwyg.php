<?php

class acf_field_qtranslate_wysiwyg extends acf_field_wysiwyg
{

	function __construct()
	{
		$this->name = 'qtranslate_wysiwyg';
		$this->label = __("Wysiwyg Editor",'acf');
		$this->category = __("qTranslate",'acf');
		$this->defaults = array(
			'tabs'			=> 'all',
			'toolbar'		=> 'full',
			'media_upload' 	=> 1,
			'default_value'	=> '',
		);


    	// Create an acf version of the_content filter (acf_the_content)
		if(	!empty($GLOBALS['wp_embed']) ) {

			add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'run_shortcode' ), 8 );
			add_filter( 'acf_the_content', array( $GLOBALS['wp_embed'], 'autoembed' ), 8 );

		}

		add_filter( 'acf_the_content', 'capital_P_dangit', 11 );
		add_filter( 'acf_the_content', 'wptexturize' );
		add_filter( 'acf_the_content', 'convert_smilies' );
		add_filter( 'acf_the_content', 'convert_chars' );
		add_filter( 'acf_the_content', 'wpautop' );
		add_filter( 'acf_the_content', 'shortcode_unautop' );
		add_filter( 'acf_the_content', 'prepend_attachment' );
		add_filter( 'acf_the_content', 'do_shortcode', 11);

		acf_field::__construct();
	}

}


new acf_field_qtranslate_wysiwyg;
