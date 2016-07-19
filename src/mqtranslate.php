<?php

require_once ACF_QTRANSLATE_PLUGIN_DIR . 'src/acf_interface.php';

class acf_qtranslate_mqtranslate {

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

		$this->monkey_patches();
	}

	/**
	 * Monkey patches.
	 */
	public function monkey_patches() {
		global $q_config;

		// http://www.qianqin.de/qtranslate/forum/viewtopic.php?f=3&t=3497
		if (isset($q_config['js']['qtrans_switch'])) {
			if (strpos($q_config['js']['qtrans_switch'], 'originalSwitchEditors') === false) {
				$q_config['js']['qtrans_switch'] = "var _switchEditors = jQuery.extend(true, {}, switchEditors);\n" . $q_config['js']['qtrans_switch'];
				$q_config['js']['qtrans_switch'] = preg_replace("/(var vta = document\.getElementById\('qtrans_textarea_' \+ id\);)/", "\$1\nif(!vta)return _switchEditors.go(id, lang);", $q_config['js']['qtrans_switch']);
			}
		}

		// https://github.com/funkjedi/acf-qtranslate/issues/2#issuecomment-37612918
		if (isset($q_config['js']['qtrans_hook_on_tinyMCE'])) {
			if (strpos($q_config['js']['qtrans_hook_on_tinyMCE'], 'ed.editorId.match(/^qtrans_/)') === false) {
				$q_config['js']['qtrans_hook_on_tinyMCE'] = preg_replace("/(qtrans_save\(switchEditors\.pre_wpautop\(o\.content\)\);)/", "if (ed.editorId.match(/^qtrans_/)) \$1", $q_config['js']['qtrans_hook_on_tinyMCE']);
			}
		}
	}

}
