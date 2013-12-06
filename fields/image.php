<?php

class acf_field_qtranslate_image extends acf_field_image
{

	function __construct()
	{
		$this->name = 'qtranslate_image';
		$this->label = __("Image", 'acf');
		$this->category = __("qTranslate", 'acf');
		$this->defaults = array(
			'save_format'	=>	'object',
			'preview_size'	=>	'thumbnail',
			'library'		=>	'all'
		);
		$this->l10n = array(
			'select'		=>	__("Select Image",'acf'),
			'edit'			=>	__("Edit Image",'acf'),
			'update'		=>	__("Update Image",'acf'),
			'uploadedTo'	=>	__("uploaded to this post",'acf'),
		);

		acf_field::__construct();

		// filters
		add_filter('get_media_item_args', array($this, 'get_media_item_args'));
		add_filter('wp_prepare_attachment_for_js', array($this, 'wp_prepare_attachment_for_js'), 10, 3);

		// JSON
		add_action('wp_ajax_acf/fields/image/get_images', array($this, 'ajax_get_images'), 10, 1);
		add_action('wp_ajax_nopriv_acf/fields/image/get_images', array($this, 'ajax_get_images'), 10, 1);
	}

	function create_field($field)
	{
		if (!acf_qtranslate_enabled()) {
			acf_field_image::create_field($field);
			return;
		}

		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);
		$currentLanguage = qtrans_getLanguage();

		echo '<div class="multi-language-field multi-language-field-image">';

		foreach ($languages as $language) {
			$class = 'wp-switch-editor';
			if ($language === $currentLanguage) {
				$class .= ' current-language';
			}
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		$base_class = $field['class'];
		$base_name = $field['name'];
		foreach ($languages as $language) :
			$value = $values[$language];
			$o = array(
				'class' => '',
				'url' => '',
			);

			if ($value && is_numeric($value)) {
				$url = wp_get_attachment_image_src($value, $field['preview_size']);
				$o['class'] = 'active';
				$o['url'] = $url[0];
			}

			$field['class'] = $base_class;
			if ($language === $currentLanguage) {
				$field['class'] .= ' current-language';
				$o['class'] .= ' current-language';
			}

			$field['name'] = $base_name . '[' . $language . ']';

			?>
			<div class="acf-image-uploader clearfix <?php echo $o['class']; ?>" data-preview_size="<?php echo $field['preview_size']; ?>" data-library="<?php echo $field['library']; ?>" data-language="<?php echo $language; ?>" >
				<input class="acf-image-value" type="hidden" name="<?php echo $field['name']; ?>" value="<?php echo $value; ?>" />
				<div class="has-image">
					<div class="hover">
						<ul class="bl">
							<li><a class="acf-button-delete ir" href="#"><?php _e("Remove", 'acf'); ?></a></li>
							<li><a class="acf-button-edit ir" href="#"><?php _e("Edit", 'acf'); ?></a></li>
						</ul>
					</div>
					<img class="acf-image-image" src="<?php echo $o['url']; ?>" alt="" />
				</div>
				<div class="no-image">
					<p><?php _e('No image selected','acf'); ?> <input type="button" class="button add-image" value="<?php _e('Add Image','acf'); ?>" />
				</div>
			</div>
		<?php endforeach;

		echo '</div>';
	}

	function format_value($value, $post_id, $field)
	{
		return $value;
	}

	function format_value_for_api($value, $post_id, $field)
	{
		if (acf_qtranslate_enabled()) {
			$value = qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($value);
		}

		return acf_field_image::format_value_for_api($value, $post_id, $field);
	}

	function update_value($value, $post_id, $field)
	{
		if (acf_qtranslate_enabled()) {
			$value = qtrans_join($value);
		}

		return $value;
	}

	function create_options( $field )
	{
		acf_field_image::create_options($field);
	}
}

new acf_field_qtranslate_image();

