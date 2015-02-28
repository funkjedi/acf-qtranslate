<?php

class acf_qtranslate_acf_4_wysiwyg extends acf_field_wysiwyg {

	/**
	 * The plugin instance.
	 * @var \acf_qtranslate_plugin
	 */
	protected $plugin;


	/*
	 *  __construct
	 *
	 *  Set name / label needed for actions / filters
	 *
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	function __construct($plugin) {
		$this->plugin = $plugin;

		$this->name = 'qtranslate_wysiwyg';
		$this->label = __("Wysiwyg Editor",'acf');
		$this->category = __("qTranslate",'acf');

		acf_field::__construct();

		add_filter('acf/fields/wysiwyg/toolbars', array($this, 'toolbars'), 1, 1);
	}

	/*
	 *  toolbars()
	 *
	 *  This filter allowsyou to customize the WYSIWYG toolbars
	 *
	 *  @param	$toolbars - an array of toolbars
	 *
	 *  @return	$toolbars - the modified $toolbars
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	function toolbars($toolbars) {
		return parent::toolbars($toolbars);
	}

	/*
	 *  create_field()
	 *
	 *  Create the HTML interface for your field
	 *
	 *  @param	$field - an array holding all the field's data
	 *
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	function create_field($field) {
		$defaults = array(
			'toolbar'		=>	'full',
			'media_upload' 	=>	'yes',
		);
		$field = array_merge($defaults, $field);


		global $q_config, $wp_version;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);
		$currentLanguage = $this->plugin->get_active_language();

		echo '<div class="multi-language-field multi-language-field-wysiwyg">';

		foreach ($languages as $language) {
			$class = ($language === $currentLanguage) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language):
			$value = $values[$language];
			$id = 'wysiwyg' . $field['id'] . "[$language]";
			$name = $field['name'] . "[$language]";
			$class = ($language === $currentLanguage) ? 'acf_wysiwyg wp-editor-wrap current-language' : 'acf_wysiwyg wp-editor-wrap';

			?>
			<div id="wp-<?php echo $id; ?>-wrap" class="<?php echo $class; ?>" data-toolbar="<?php echo $field['toolbar']; ?>" data-upload="<?php echo $field['media_upload']; ?>" data-language="<?php echo $language; ?>">
				<?php if($field['media_upload'] == 'yes'): ?>
					<?php if( version_compare($wp_version, '3.3', '<') ): ?>
						<div id="editor-toolbar">
							<div id="media-buttons" class="hide-if-no-js">
								<?php do_action( 'media_buttons' ); ?>
							</div>
						</div>
					<?php else: ?>
						<div id="wp-<?php echo $id; ?>-editor-tools" class="wp-editor-tools">
							<div id="wp-<?php echo $id; ?>-media-buttons" class="hide-if-no-js wp-media-buttons">
								<?php do_action( 'media_buttons' ); ?>
							</div>
						</div>
					<?php endif; ?>
				<?php endif; ?>
				<div id="wp-<?php echo $id; ?>-editor-container" class="wp-editor-container">
					<textarea id="<?php echo $id; ?>" class="qtx-wp-editor-area" name="<?php echo $name; ?>" ><?php echo wp_richedit_pre($value); ?></textarea>
				</div>
			</div>
		<?php endforeach;

		echo '</div>';
	}

	/*
	 *  format_value
	 *
	 *  @description: uses the basic value and allows the field type to format it
	 *  @since: 3.6
	 *  @created: 26/01/13
	 */
	function format_value($value, $post_id, $field) {
		return $value;
	}

	/*
	 *  format_value_for_api()
	 *
	 *  This filter is appied to the $value after it is loaded from the db and before it is passed back to the api functions such as the_field
	 *
	 *  @type	filter
	 *  @since	3.6
	 *  @date	23/01/13
	 *
	 *  @param	$value	- the value which was loaded from the database
	 *  @param	$post_id - the $post_id from which the value was loaded
	 *  @param	$field	- the field array holding all the field options
	 *
	 *  @return	$value	- the modified value
	 */
	function format_value_for_api($value, $post_id, $field) {
		$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
		return parent::format_value_for_api($value, $post_id, $field);
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
