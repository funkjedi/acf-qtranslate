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


	function render_field($field)
	{
		if (!acf_qtranslate_enabled()) {
			acf_field_wysiwyg::render_field($field);
			return;
		}

		// enqueue
		acf_enqueue_uploader();


		// vars
		$id = $field['id'] . '-' . uniqid();
		$mode = 'html';
		$show_tabs = true;


		// get height
		$height = acf_get_user_setting('wysiwyg_height', 300);
		$height = max( $height, 300 ); // minimum height is 300


		// detect mode
		if( $field['tabs'] == 'visual' ) {

			// case: visual tab only
			$mode = 'tmce';
			$show_tabs = false;

		} elseif( $field['tabs'] == 'text' ) {

			// case: text tab only
			$show_tabs = false;

		} elseif( wp_default_editor() == 'tinymce' ) {

			// case: both tabs
			$mode = 'tmce';

		}


		// mode
		$switch_class = $mode . '-active';


		// filter value for editor
		remove_all_filters( 'acf_the_editor_content' );

		if( $mode == 'tmce' ) {

			add_filter('acf_the_editor_content', 'wp_richedit_pre');

		} else {

			add_filter('acf_the_editor_content', 'wp_htmledit_pre');

		}

		$field['value'] = apply_filters( 'acf_the_editor_content', $field['value'] );


		global $q_config, $wp_version;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);

		echo '<div class="multi-language-field multi-language-field-wysiwyg">';

		foreach ($languages as $language) {
			$class = ($language === end($languages)) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		$uid = $field['id'] . '-' . uniqid();
		foreach ($languages as $language):
			$value = $values[$language];
			$id = $field['id'] . "-$language";
			$name = $field['name'] . "[$language]";
			if ($language === end($languages)) {
				$switch_class .= ' current-language';
			}

			?>
			<div id="wp-<?php echo $id; ?>-wrap" class="acf-wysiwyg-wrap wp-core-ui wp-editor-wrap <?php echo $switch_class; ?>" data-toolbar="<?php echo $field['toolbar']; ?>" data-upload="<?php echo $field['media_upload']; ?>" data-language="<?php echo $language; ?>">
				<div id="wp-<?php echo $id; ?>-editor-tools" class="wp-editor-tools hide-if-no-js">
					<?php if( $field['media_upload'] ): ?>
					<div id="wp-<?php echo $id; ?>-media-buttons" class="wp-media-buttons">
						<?php do_action( 'media_buttons' ); ?>
					</div>
					<?php endif; ?>
					<?php if( user_can_richedit() && $show_tabs ): ?>
						<div class="wp-editor-tabs">
							<a id="<?php echo $id; ?>-html" class="wp-switch-editor switch-html" onclick="switchEditors.switchto(this);"><?php echo _x( 'Text', 'Name for the Text editor tab (formerly HTML)' ); ?></a>
							<a id="<?php echo $id; ?>-tmce" class="wp-switch-editor switch-tmce" onclick="switchEditors.switchto(this);"><?php echo __('Visual'); ?></a>
						</div>
					<?php endif; ?>
				</div>
				<div id="wp-<?php echo $id; ?>-editor-container" class="wp-editor-container">
					<textarea id="<?php echo $id; ?>" class="wp-editor-area" name="<?php echo $name; ?>" <?php if($height): ?>style="height:<?php echo $height; ?>px;"<?php endif; ?>><?php echo $value; ?></textarea>
				</div>
			</div>
		<?php endforeach;

		echo '</div>';
	}


  	function input_form_data( $args ) {

	   	// vars
		$json = array();
		$toolbars = $this->get_toolbars();


		// loop through toolbars
		if( !empty($toolbars) ) {

			foreach( $toolbars as $label => $rows ) {

				// vars
				$label = sanitize_title( $label );
				$label = str_replace('-', '_', $label);


				// append to $json
				$json[ $label ] = array();


				// convert to strings
				if( !empty($rows) ) {

					foreach( $rows as $i => $row ) {

						$json[ $label ][ $i ] = implode(',', $row);

					}
					// foreach

				}
				// if

			}
			// foreach

		}
		// if

		?>
		<script type="text/javascript">
		(function($) {

			acf.fields.qtranslate_wysiwyg.toolbars = <?php echo json_encode( $json ); ?>;

		})(jQuery);
		</script>
		<?php

   	}


	function update_value($value, $post_id, $field)
	{
		if (acf_qtranslate_enabled()) {
			$value = qtrans_join($value);
		}

		return $value;
	}

}


new acf_field_qtranslate_wysiwyg;
