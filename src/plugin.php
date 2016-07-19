<?php

class acf_qtranslate_plugin {

	/**
	 * An ACF instance.
	 * @var \acf_qtranslate_acf_interface
	 */
	protected $acf;


	/**
	 * Create an instance.
	 * @return void
	 */
	public function __construct() {
		add_action('plugins_loaded',                  array($this, 'plugins_loaded'), 3);
		add_action('acf/input/admin_enqueue_scripts', array($this, 'admin_enqueue_scripts'));
		add_action('admin_menu',                      array($this, 'admin_menu'));
		add_action('admin_init',                      array($this, 'admin_init'));

		add_filter('plugin_action_links_' . plugin_basename(ACF_QTRANSLATE_PLUGIN), array($this, 'plugin_action_links'));
	}

	/**
	 * Setup plugin if Advanced Custom Fields is enabled.
	 * @return void
	 */
	public function plugins_loaded() {
		if ($this->acf_enabled() && $this->qtranslate_variant_enabled()) {

			// setup qtranslate fields for ACF 4
			if ($this->acf_major_version() === 4) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/acf_4/acf.php';
				$this->acf = new acf_qtranslate_acf_4($this);
			}

			// setup qtranslate fields for ACF 5
			if ($this->acf_major_version() === 5) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/acf_5/acf.php';
				$this->acf = new acf_qtranslate_acf_5($this);
			}

			// setup mqtranslate integration
			if ($this->mqtranslate_enabled()) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/mqtranslate.php';
				new acf_qtranslate_mqtranslate($this, $this->acf);
			}

			// setup ppqtranslate integration
			if ($this->ppqtranslate_enabled()) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/ppqtranslate.php';
				new acf_qtranslate_ppqtranslate($this, $this->acf);
			}

			// setup qtranslatex integration
			if ($this->qtranslatex_enabled()) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/qtranslatex.php';
				new acf_qtranslate_qtranslatex($this, $this->acf);
			}

		}
	}

	/**
	 * Check whether the plugin is active by checking the active_plugins list.
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 * @return bool True, if in the active plugins list. False, not in the list.
	 */
	public function is_plugin_active($plugin) {
		return in_array($plugin, (array)get_option('active_plugins', array())) || $this->is_plugin_active_for_network($plugin);
	}

	/**
	 * Check whether the plugin is active for the entire network.
	 *
	 * @param string $plugin Base plugin path from plugins directory.
	 * @return bool True, if active for the network, otherwise false.
	 */
	public function is_plugin_active_for_network($plugin) {
		if (!is_multisite()) {
			return false;
		}
		$plugins = get_site_option('active_sitewide_plugins');
		if (isset($plugins[$plugin])) {
			return true;
		}
		return false;
	}

	/**
	 * Check if Advanced Custom Fields is enabled.
	 * @return boolean
	 */
	public function acf_enabled() {
		if (function_exists('acf')) {
			return $this->acf_major_version() === 4 || $this->acf_major_version() === 5;
		}
		return false;
	}

	/**
	 * Return the major version number for Advanced Custom Fields.
	 * @return int
	 */
	public function acf_major_version() {
		return (int) acf()->settings['version'][0];
	}

	/**
	 * Check if a qTranslate variant is enabled.
	 * @return boolean
	 */
	public function qtranslate_variant_enabled() {
		$plugins = array(
			'qtranslate/qtranslate.php',
			'ztranslate/ztranslate.php',
			'mqtranslate/mqtranslate.php',
			'qtranslate-x/qtranslate.php',
			'qtranslate-xp/ppqtranslate.php',
		);
		foreach ($plugins as $identifier) {
			if ($this->is_plugin_active($identifier)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if qTranslate Plus is enabled.
	 */
	public function ppqtranslate_enabled() {
		return function_exists('ppqtrans_getLanguage');
	}

	/**
	 * Check if qTranslate-X is enabled.
	 */
	public function qtranslatex_enabled() {
		return function_exists('qtranxf_getLanguage');
	}

	/**
	 * Check if mqTranslate is enabled.
	 */
	public function mqtranslate_enabled() {
		return function_exists('mqtrans_currentUserCanEdit');
	}

	/**
	 * Get the active language.
	 */
	public function get_active_language() {
		return apply_filters('acf_qtranslate_get_active_language', qtrans_getLanguage());
	}

	/**
	 * Load javascript and stylesheets on admin pages.
	 */
	public function admin_enqueue_scripts() {
		wp_enqueue_style('acf_qtranslate_common',  plugins_url('/assets/common.css', ACF_QTRANSLATE_PLUGIN), array('acf-input'));
		wp_enqueue_script('acf_qtranslate_common', plugins_url('/assets/common.js',  ACF_QTRANSLATE_PLUGIN), array('acf-input','underscore'));
	}

	/**
	 * Add settings link on plugin page.
	 * @param array
	 * @return array
	 */
	public function plugin_action_links($links) {
		array_unshift($links, '<a href="options-general.php?page=acf-qtranslate">Settings</a>');
		return $links;
	}

	/**
	 * Retrieve the value of a plugin setting.
	 */
	function get_plugin_setting($name, $default = null) {
		$options = get_option('acf_qtranslate');
		if (isset($options[$name]) === true) {
			return $options[$name];
		}
		return $default;
	}

	/**
	 * Register the options page with the Wordpress menu.
	 */
	function admin_menu() {
		add_options_page('ACF qTranslate', 'ACF qTranslate', 'manage_options', 'acf-qtranslate', array($this, 'options_page'));
	}

	/**
	 * Register settings and default fields.
	 */
	function admin_init() {
		register_setting('acf_qtranslate', 'acf_qtranslate');

		add_settings_section(
			'qtranslatex_section',
			'qTranslate-X',
			array($this, 'render_section_qtranslatex'),
			'acf_qtranslate'
		);

		add_settings_field(
			'translate_standard_field_types',
			'Enable translation for Standard Field Types',
			array($this, 'render_setting_translate_standard_field_types'),
			'acf_qtranslate',
			'qtranslatex_section'
		);

		add_settings_field(
			'show_language_tabs',
			'Display language tabs',
			array($this, 'render_setting_show_language_tabs'),
			'acf_qtranslate',
			'qtranslatex_section'
		);
	}

	/**
	 * Render the options page.
	 */
	function options_page() {
		?>
		<form action="options.php" method="post">
			<h2>ACF qTranslate Settings</h2>
			<br>
			<?php

			settings_fields('acf_qtranslate');
			do_settings_sections('acf_qtranslate');
			submit_button();

			?>
		</form>
		<?php
	}

	/**
	 * Render the qTranslate-X section.
	 */
	function render_section_qtranslatex() {
		?>
		The following options represent additional functionality that is available when
		using qTranslate-X. These functionality is off by default and must be enabled below.
		<?php
	}

	/**
	 * Render setting.
	 */
	function render_setting_translate_standard_field_types() {
		?>
		<input type="checkbox" name="acf_qtranslate[translate_standard_field_types]" <?php checked($this->get_plugin_setting('translate_standard_field_types'), 1); ?> value="1">
		<?php
	}

	/**
	 * Render setting.
	 */
	function render_setting_show_language_tabs() {
		?>
		<input type="checkbox" name="acf_qtranslate[show_language_tabs]" <?php checked($this->get_plugin_setting('show_language_tabs'), 1); ?> value="1">
		<?php
	}

}
