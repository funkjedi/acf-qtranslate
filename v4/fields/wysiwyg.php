<?php

class acf_field_qtranslate_wysiwyg extends acf_field_wysiwyg
{

	function __construct()
	{
		$this->name = 'qtranslate_wysiwyg';
		$this->label = __("Wysiwyg Editor",'acf');
		$this->category = __("qTranslate",'acf');

		acf_field::__construct();

		add_filter('acf/fields/wysiwyg/toolbars', array($this, 'toolbars'), 1, 1);
	}


	function toolbars($toolbars)
	{
		return acf_field_wysiwyg::toolbars($toolbars);
	}


	function create_field($field)
	{
		if (!acf_qtranslate_enabled()) {
			acf_field_wysiwyg::create_field($field);
			return;
		}

		$defaults = array(
			'toolbar'		=>	'full',
			'media_upload' 	=>	'yes',
		);
		$field = array_merge($defaults, $field);


		global $q_config, $wp_version;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);

		echo '<div class="multi-language-field multi-language-field-wysiwyg">';

		foreach ($languages as $language) {
			$class = ($language === end($languages)) ? 'wp-switch-editor current-language' : 'wp-switch-editor';
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language):
			$value = $values[$language];
			$id = 'wysiwyg' . $field['id'] . "[$language]";
			$name = $field['name'] . "[$language]";
			$class = ($language === end($languages)) ? 'acf_wysiwyg wp-editor-wrap current-language' : 'acf_wysiwyg wp-editor-wrap';

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
					<textarea id="<?php echo $id; ?>" class="wp-editor-area" name="<?php echo $name; ?>" ><?php echo wp_richedit_pre($value); ?></textarea>
				</div>
			</div>
		<?php endforeach;

		echo '</div>';
	}


	function format_value($value, $post_id, $field)
	{
		return $value;
	}


	function format_value_for_api($value, $post_id, $field) {
		if (acf_qtranslate_enabled()) {
			$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
		}

		return acf_field_wysiwyg::format_value_for_api($value, $post_id, $field);
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
