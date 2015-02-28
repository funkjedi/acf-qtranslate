<?php

require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/acf_interface.php';

class acf_qtranslate_ppqtranslate {

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
		require_once ACF_QTRANSLATE_PLUGIN_DIR . 'compatibility/ppqtranslate.php';

	}

}
