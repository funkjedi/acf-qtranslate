<?php

class acf_qtranslate_plugin {

	/**
	 * Create an instance.
	 * @return void
	 */
	public function __construct() {
		add_action('plugins_loaded', array($this, 'plugins_loaded'), 3);
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
				$acf = new acf_qtranslate_acf_4($this);
			}

			// setup qtranslate fields for ACF 5
			if ($this->acf_major_version() === 5) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/acf_5/acf.php';
				$acf = new acf_qtranslate_acf_5($this);
			}

			// setup ppqtranslate integration
			if ($this->ppqtranslate_enabled()) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/ppqtranslate.php';
				new acf_qtranslate_ppqtranslate($this, $acf);
			}

			// setup qtranslatex integration
			if ($this->qtranslatex_enabled()) {
				require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/qtranslatex.php';
				new acf_qtranslate_qtranslatex($this, $acf);
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

}
