<?php

namespace acf_qtranslate;

use acf_qtranslate\acf_4\acf as acf_4;
use acf_qtranslate\acf_5\acf as acf_5;

class plugin {

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
				$acf = new acf_4;
			}

			// setup qtranslate fields for ACF 5
			if ($this->acf_major_version() === 5) {
				$acf = new acf_5;
			}

			// setup qtranslatex integration
			if ($this->qtranslatex_enabled()) {
				new qtranslatex($this, $acf);
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
		foreach ($plugins as $name => $identifier) {
			if ($this->is_plugin_active($identifier)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Check if qTranslate-X is enabled
	 */
	public function qtranslatex_enabled() {
		return defined('QTX_VERSION');
	}

}
