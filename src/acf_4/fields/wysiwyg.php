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
		$this->defaults = array(
			'toolbar'		=>	'full',
			'media_upload' 	=>	'yes',
			'default_value'	=>	'',
		);

		acf_field::__construct();
	}

	/*
	 *  input_admin_head()
	 *
	 *  This action is called in the admin_head action on the edit screen where your field is created.
	 *  Use this action to add css and javascript to assist your create_field() action.
	 *
	 *  @info	http://codex.wordpress.org/Plugin_API/Action_Reference/admin_head
	 *  @type	action
	 *  @since	3.6
	 *  @date	23/01/13
	 */
	function input_admin_head() {}

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
			$id = 'wysiwyg-' . $field['id'] . '-' . uniqid();
			$name = $field['name'] . "[$language]";
			$class = ($language === $currentLanguage) ? 'acf_wysiwyg wp-editor-wrap current-language' : 'acf_wysiwyg wp-editor-wrap';

			?>
			<div id="wp-<?php echo $id; ?>-wrap" class="<?php echo $class; ?>" data-toolbar="<?php echo $field['toolbar']; ?>" data-upload="<?php echo $field['media_upload']; ?>" data-language="<?php echo $language; ?>">
				<?php if( user_can_richedit() && $field['media_upload'] == 'yes' ): ?>
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
					<textarea id="<?php echo $id; ?>" class="qtx-wp-editor-area" name="<?php echo $name; ?>" ><?php

					if( user_can_richedit() )
					{
						echo wp_richedit_pre( $field['value'] );
					}
					else
					{
						echo wp_htmledit_pre( $field['value'] );
					}

					?></textarea>
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
