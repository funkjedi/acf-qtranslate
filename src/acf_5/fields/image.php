<?php

class acf_qtranslate_acf_5_image extends acf_field_image {

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

		$this->name = 'qtranslate_image';
		$this->label = __("Image", 'acf');
		$this->category = __("qTranslate", 'acf');
		$this->defaults = array(
			'return_format' => 'array',
			'preview_size'  => 'thumbnail',
			'library'       => 'all',
			'min_width'     => 0,
			'min_height'    => 0,
			'min_size'      => 0,
			'max_width'     => 0,
			'max_height'    => 0,
			'max_size'      => 0,
			'mime_types'    => ''
		);
		$this->l10n = array(
			'select'     => __("Select Image",'acf'),
			'edit'       => __("Edit Image",'acf'),
			'update'     => __("Update Image",'acf'),
			'uploadedTo' => __("Uploaded to this post",'acf'),
			'all'        => __("All images",'acf'),
		);


		// filters
		add_filter('get_media_item_args',			array($this, 'get_media_item_args'));
		add_filter('wp_prepare_attachment_for_js',	array($this, 'wp_prepare_attachment_for_js'), 10, 3);

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
		global $q_config;
		$languages = qtrans_getSortedLanguages(true);
		$values = qtrans_split($field['value'], $quicktags = true);
		$currentLanguage = $this->plugin->get_active_language();

		// enqueue
		acf_enqueue_uploader();

		// vars
		$div = array(
			'class'					=> 'acf-image-uploader acf-cf',
			'data-preview_size'		=> $field['preview_size'],
			'data-library'			=> $field['library'],
			'data-mime_types'		=> $field['mime_types']
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
			$div['data-language'] = $language;
			$div['class'] = 'acf-image-uploader acf-cf';

			// has value?
			if( $field['value'] && is_numeric($field['value']) ) {
				$url = wp_get_attachment_image_src($field['value'], $field['preview_size']);
				$url = $url[0];

				$div['class'] .= ' has-value';
			}

			// basic?
			$basic = !current_user_can('upload_files');
			if ($basic) {
				$div['class'] .= ' basic';
			}

			if ($language === $currentLanguage) {
				$div['class'] .= ' current-language';
			}

			?>
			<div <?php acf_esc_attr_e( $div ); ?>>
				<div class="acf-hidden">
					<?php acf_hidden_input(array( 'name' => $input_atts['name'], 'value' => $field['value'], 'data-name' => 'id' )); ?>
				</div>
				<div class="view show-if-value acf-soh">
					<img data-name="image" src="<?php echo $url; ?>" alt=""/>
					<ul class="acf-hl acf-soh-target">
						<?php if( !$basic ): ?>
							<li><a class="acf-icon dark" data-name="edit" href="#"><i class="acf-sprite-edit"></i></a></li>
						<?php endif; ?>
						<li><a class="acf-icon dark" data-name="remove" href="#"><i class="acf-sprite-delete"></i></a></li>
					</ul>
				</div>
				<div class="view hide-if-value">
					<?php if( $basic ): ?>
						<?php if( $field['value'] && !is_numeric($field['value']) ): ?>
							<div class="acf-error-message"><p><?php echo $field['value']; ?></p></div>
						<?php endif; ?>
						<input type="file" name="<?php echo $field['name']; ?>" id="<?php echo $field['id']; ?>" />
					<?php else: ?>
						<p style="margin:0;"><?php _e('No image selected','acf'); ?> <a data-name="add" class="acf-button" href="#"><?php _e('Add Image','acf'); ?></a></p>
					<?php endif; ?>
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
		$value = parent::update_value($value, $post_id, $field);
		return qtrans_join($value);
	}

}
