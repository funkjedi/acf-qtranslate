<?php

require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/acf_interface.php';

class acf_qtranslate_qtranslatex {

	/**
	 * An ACF instance.
	 * @var \acf_qtranslate_acf_interface
	 */
	protected $acf;

	/**
	 * The plugin instance.
	 * @var \acf_qtranslate_plugin
	 */
	protected $plugin;


	/**
	 * Create an instance.
	 * @return void
	 */
	public function __construct(acf_qtranslate_plugin $plugin, acf_qtranslate_acf_interface $acf) {
		$this->acf = $acf;
		$this->plugin = $plugin;

		// include compatibility functions
		require_once ACF_QTRANSLATE_PLUGIN_DIR . 'compatibility/qtranslatex.php';

		add_action('admin_head',                         array($this, 'admin_head'));
		add_action('admin_footer',                       array($this, 'admin_footer'), 9999); //after qTranslate-X
		add_filter('qtranslate_custom_admin_js',         array($this, 'qtranslate_custom_admin_js'));
		add_filter('qtranslate_load_admin_page_config',  array($this, 'qtranslate_load_admin_page_config'));
		add_filter('acf_qtranslate_get_active_language', array($this, 'get_active_language'));
	}

	/**
	 * Add class to the body element.
	 */
	public function admin_head() {
		?>
		<style>
		.multi-language-field {margin-top:0!important;}
		.multi-language-field .wp-switch-editor[data-language] {display:none!important;}
		</style>
		<?php
	}

	/**
	 * Load custom version of edit-post.js if needed.
	 */
	public function admin_footer() {
		if (wp_script_is('qtranslate-admin-edit')) {

			//$handle = wp_script_is('qtranslate-admin-edit', 'registered');

			//wp_register_script('qtranslate-admin-edit', $script_url, array(), QTX_VERSION);
			//wp_enqueue_script('qtranslate-admin-edit');

		}
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
		global $pagenow, $plugin_page;

		if ($pagenow === 'admin.php' && isset($plugin_page)) {
			return 'admin/js/edit-post';
		}
	}

	/**
	 * Get the active language.
	 */
	public function get_active_language($language) {
		if (empty($_COOKIE['qtrans_edit_language']) === false) {
			$enabledLanguages = qtrans_getSortedLanguages();
			if (in_array($_COOKIE['qtrans_edit_language'], $enabledLanguages)) {
				$language = $_COOKIE['qtrans_edit_language'];
			}
		}
		return $language;
	}

}
