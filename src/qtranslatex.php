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

		// load qtranslate compatibility layer
		require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/qtranslatex/qtranslate-compatibility.php';

		add_filter('admin_head',                        array($this, 'admin_head'));
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
		array_push($configs, array(
			'pages' => array($pagenow => ''),
			'forms' => array(array('fields' => $fields))
		));

		return $configs;
	}

}
