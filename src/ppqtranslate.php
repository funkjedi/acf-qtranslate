<?php

namespace acf_qtranslate;

class ppqtranslate {

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
		require_once ACF_QTRANSLATE_PLUGIN_DIR . 'compatibility/ppqtranslate.php';

	}

}
