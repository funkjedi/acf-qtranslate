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

		$this->monkey_patches();
	}

	/**
	 * Monkey patches.
	 */
	public function monkey_patches() {
		global $q_config;

		// http://www.qianqin.de/qtranslate/forum/viewtopic.php?f=3&t=3497
		if (isset($q_config['js']['ppqtrans_switch'])) {
			if (strpos($q_config['js']['ppqtrans_switch'], '_switchEditors') === false) {
				$q_config['js']['ppqtrans_switch'] = "var _switchEditors = jQuery.extend(true, {}, switchEditors);\n" . $q_config['js']['ppqtrans_switch'];
				$q_config['js']['ppqtrans_switch'] = preg_replace("/(var vta = document\.getElementById\('ppqtrans_textarea_' \+ id\);)/", "\$1\nif(!vta)return _switchEditors.go(id, lang);", $q_config['js']['ppqtrans_switch']);
			}
		}

		// https://github.com/funkjedi/acf-qtranslate/issues/2#issuecomment-37612918
		if (isset($q_config['js']['ppqtrans_hook_on_tinyMCE'])) {
			if (strpos($q_config['js']['ppqtrans_hook_on_tinyMCE'], 'ed.id.match(/^ppqtrans_/)') === false) {
				$q_config['js']['ppqtrans_hook_on_tinyMCE'] = preg_replace("/(ppqtrans_save\(switchEditors\.pre_wpautop\(e\.content\)\);)/", "if (ed.id.match(/^ppqtrans_/)) \$1", $q_config['js']['ppqtrans_hook_on_tinyMCE']);
			}
		}
	}

}
