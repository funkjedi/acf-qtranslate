<?php

namespace acf_qtranslate;

class qtranslatex {

	/**
	 * An ACF instance.
	 * @var \acf_qtranslate\acf_interface
	 */
	protected $acf;

	/**
	 * The plugin instance.
	 * @var \acf_qtranslate\plugin
	 */
	protected $plugin;


	/**
	 * Create an instance.
	 * @return void
	 */
	public function __construct(plugin $plugin, acf_interface $acf) {
		$this->acf = $acf;
		$this->plugin = $plugin;

		// include compatibility functions
		require_once ACF_QTRANSLATE_PLUGIN_DIR . 'compatibility/qtranslatex.php';

		add_filter('admin_head',                        array($this, 'admin_head'));
		add_filter('qtranslate_custom_admin_js',        array($this, 'qtranslate_custom_admin_js'));
		add_filter('qtranslate_load_admin_page_config', array($this, 'qtranslate_load_admin_page_config'));
	}

	/**
	 * Add class to the body element.
	 */
	public function admin_head() {
		?>
		<style>
		.multi-language-field {margin-top:0!important;}
		.multi-language-field .wp-switch-editor {display:none!important;}
		</style>
		<?php
	}

	/**
	 * Load ACF form element ids into qTranslate-X.
	 * @return void
	 */
	public function qtranslate_load_admin_page_config($configs) {
		global $pagenow;

		$fields = $this->acf->get_visible_acf_fields();
		if (count($fields)) {
			// ACF uses a single tinyMCE editor mceInit
			// for all it's WYSIWYG fields
			$fields[] = array('id' => 'acf_settings');

			array_push($configs, array(
				'pages' => array($pagenow => ''),
				'forms' => array(array('fields' => $fields))
			));
		}

		return $configs;
	}

	/**
	 * Use the edit-post script on admin pages.
	 * @return string
	 */
	public function qtranslate_custom_admin_js() {
		global $pagenow;

		if ($pagenow === 'admin.php' && isset($_GET['page'])) {
			return 'admin/js/edit-post';
		}
	}

}
