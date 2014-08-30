<?php

class acf_field_qtranslate_image extends acf_field_image
{

	function __construct()
	{
		$this->name = 'qtranslate_image';
		$this->label = __("Image", 'acf');
		$this->category = __("qTranslate", 'acf');
		$this->defaults = array(
			'return_format'	=> 'array',
			'preview_size'	=> 'thumbnail',
			'library'		=> 'all'
		);
		$this->l10n = array(
			'select'		=> __("Select Image",'acf'),
			'edit'			=> __("Edit Image",'acf'),
			'update'		=> __("Update Image",'acf'),
			'uploadedTo'	=> __("uploaded to this post",'acf'),
		);

		acf_field::__construct();

		// filters
		add_filter('get_media_item_args',			array($this, 'get_media_item_args'));
		add_filter('wp_prepare_attachment_for_js',	array($this, 'wp_prepare_attachment_for_js'), 10, 3);
	}

	function render_field($field)
	{
		if (!acf_qtranslate_enabled()) {
			acf_field_image::render_field($field);
			return;
		}

		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);
		$currentLanguage = qtrans_getLanguage();


		// enqueue
		acf_enqueue_uploader();


		// vars
		$div_atts = array(
			'class'					=> 'acf-image-uploader acf-cf',
			'data-preview_size'		=> $field['preview_size'],
			'data-library'			=> $field['library']
		);
		$input_atts = array(
			'type'					=> 'hidden',
			'name'					=> $field['name'],
			'value'					=> $field['value'],
			'data-name'				=> 'value-id'
		);
		$url = '';


		echo '<div class="multi-language-field multi-language-field-image">';

		foreach ($languages as $language) {
			$class = 'wp-switch-editor';
			if ($language === $currentLanguage) {
				$class .= ' current-language';
			}
			echo '<a class="' . $class . '" data-language="' . $language . '">' . $q_config['language_name'][$language] . '</a>';
		}

		foreach ($languages as $language):

			$input_atts['name'] = $field['name'] . '[' . $language . ']';
			$field['value'] = $values[$language];
			$div_atts['data-language'] = $language;
			$div_atts['class'] = 'acf-image-uploader acf-cf';

			// has value?
			if( $field['value'] && is_numeric($field['value']) ) {

				$url = wp_get_attachment_image_src($field['value'], $field['preview_size']);
				$url = $url[0];

				$div_atts['class'] .= ' has-value';

			}

			if ($language === $currentLanguage) {
				$div_atts['class'] .= ' current-language';
			}

			?>
			<div <?php acf_esc_attr_e( $div_atts ); ?>>
				<div class="acf-hidden">
					<input <?php acf_esc_attr_e( $input_atts ); ?>/>
				</div>
				<div class="view show-if-value acf-soh">
					<ul class="acf-hl acf-soh-target">
						<li><a class="acf-icon dark" data-name="edit-button" href="#"><i class="acf-sprite-edit"></i></a></li>
						<li><a class="acf-icon dark" data-name="remove-button" href="#"><i class="acf-sprite-delete"></i></a></li>
					</ul>
					<img data-name="value-url" src="<?php echo $url; ?>" alt=""/>
				</div>
				<div class="view hide-if-value">
					<p><?php _e('No image selected','acf'); ?> <a data-name="add-button" class="acf-button" href="#"><?php _e('Add Image','acf'); ?></a></p>
				</div>
			</div>

		<?php endforeach;

		echo '</div>';
	}


	function update_value($value, $post_id, $field)
	{
		$value = acf_field_image::update_value($value, $post_id, $field);

		if (acf_qtranslate_enabled()) {
			$value = qtrans_join($value);
		}

		return $value;
	}

}

new acf_field_qtranslate_image();

