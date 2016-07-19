<?php

class acf_qtranslate_acf_5_wysiwyg extends acf_field_wysiwyg {

	/**
	 * The plugin instance.
	 * @var \acf_qtranslate_plugin
	 */
	protected $plugin;


	/*
	 *  __construct
	 *
	 *  This function will setup the field type data
	 *
	 *  @type	function
	 *  @date	5/03/2014
	 *  @since	5.0.0
	 *
	 *  @param	n/a
	 *  @return	n/a
	 */
	function __construct($plugin) {
		$this->plugin = $plugin;

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

		// actions
		add_action('acf/input/admin_footer_js', 	array($this, 'input_admin_footer_js'));

		acf_field::__construct();
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

		// enqueue
		acf_enqueue_uploader();

		// vars
		$id = uniqid('acf-editor-');
		//$id = $field['id'] . '-' . uniqid();
		$mode = 'html';
		$show_tabs = true;

		// get height
		$height = acf_get_user_setting('wysiwyg_height', 300);
		$height = max( $height, 300 ); // minimum height is 300

		// detect mode
		// case: visual tab only
		if ($field['tabs'] == 'visual') {
			$mode = 'tmce';
			$show_tabs = false;
		}
		// case: text tab only
		elseif ($field['tabs'] == 'text') {
			$show_tabs = false;
		}
		// case: both tabs
		elseif (wp_default_editor() == 'tinymce') {
			$mode = 'tmce';
		}

		// mode
		$switch_class = $mode . '-active';

		// filter value for editor
		remove_all_filters('acf_the_editor_content');

		if ($mode == 'tmce') {
			add_filter('acf_the_editor_content', 'wp_richedit_pre');
		}
		else {
			add_filter('acf_the_editor_content', 'wp_htmledit_pre');
		}

		global $q_config, $wp_version;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);
		$currentLanguage = $this->plugin->get_active_language();

		echo '<div class="multi-language-field multi-language-field-wysiwyg">';

		foreach ($languages as $language) {
			$class = ($language === $currentLanguage) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		$uid = uniqid('acf-editor-');
		foreach ($languages as $language):
			$value = apply_filters('acf_the_editor_content', $values[$language]);
			$id = $uid . "-$language";
			$name = $field['name'] . "[$language]";
			$class = $switch_class;
			if ($language === $currentLanguage) {
				$class .= ' current-language';
			}

			?>
			<div id="wp-<?php echo $id; ?>-wrap" class="acf-editor-wrap wp-core-ui wp-editor-wrap <?php echo $class; ?>" data-toolbar="<?php echo $field['toolbar']; ?>" data-upload="<?php echo $field['media_upload']; ?>" data-language="<?php echo $language; ?>">
				<div id="wp-<?php echo $id; ?>-editor-tools" class="wp-editor-tools hide-if-no-js">
					<?php if( $field['media_upload'] ): ?>
					<div id="wp-<?php echo $id; ?>-media-buttons" class="wp-media-buttons">
						<?php do_action( 'media_buttons' ); ?>
					</div>
					<?php endif; ?>
					<?php if( user_can_richedit() && $show_tabs ): ?>
						<div class="wp-editor-tabs">
							<button id="<?php echo $id; ?>-tmce" class="wp-switch-editor switch-tmce" onclick="switchEditors.switchto(this);" type="button"><?php echo __('Visual', 'acf'); ?></button>
							<button id="<?php echo $id; ?>-html" class="wp-switch-editor switch-html" onclick="switchEditors.switchto(this);" type="button"><?php echo _x( 'Text', 'Name for the Text editor tab (formerly HTML)', 'acf' ); ?></button>
						</div>
					<?php endif; ?>
				</div>
				<div id="wp-<?php echo $id; ?>-editor-container" class="wp-editor-container">
					<textarea id="<?php echo $id; ?>" class="qtx-wp-editor-area" name="<?php echo $name; ?>" <?php if($height): ?>style="height:<?php echo $height; ?>px;"<?php endif; ?>><?php echo $value; ?></textarea>
				</div>
			</div>

		<?php endforeach;

		echo '</div>';
	}

	/*
	 *  update_value()
	 *
	 *  This filter is appied to the $value before it is updated in the db
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value - the value which will be saved in the database
	 *  @param	$post_id - the $post_id of which the value will be saved
	 *  @param	$field - the field array holding all the field options
	 *
	 *  @return	$value - the modified value
	 */
	function update_value($value, $post_id, $field) {
		return qtrans_join($value);
	}

}
