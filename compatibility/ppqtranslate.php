<?php

// provide qTranslate compatibility when using qTranslate Plus

if (function_exists('qtrans_getLanguage') === false):
	function qtrans_getLanguage() {
		return ppqtrans_getLanguage();
	}
endif;

if (function_exists('qtrans_getSortedLanguages') === false):
	function qtrans_getSortedLanguages($reverse = false) {
		return ppqtrans_getSortedLanguages($reverse);
	}
endif;

if (function_exists('qtrans_join') === false):
	function qtrans_join($texts) {
		return ppqtrans_join($texts);
	}
endif;

if (function_exists('qtrans_split') === false):
	function qtrans_split($text, $quicktags = true) {
		return ppqtrans_split($text, $quicktags);
	}
endif;

if (function_exists('qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage') === false):
	function qtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($content) {
		return ppqtrans_useCurrentLanguageIfNotFoundUseDefaultLanguage($content);
	}
endif;
