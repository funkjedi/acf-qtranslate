jQuery(function($) {

	var $body = $('body');


	/**
	 * Sync qtranslate language switchers with qtranslatex language switchers.
	 */
	$body.on('click', '.qtranxs-lang-switch', function() {
		var parent = $('.multi-language-field'), language = $(this).attr('lang');

		parent.find('.current-language').removeClass('current-language');
		parent.find('[data-language="' + language + '"]').addClass('current-language');
		parent.find('input[data-language="' + language + '"], textarea[data-language="' + language + '"]');
	});

	/**
	 * Setup qtranslate language switchers.
	 */
	$body.on('click', '.wp-switch-editor[data-language]', function() {
		var parent = $(this).parent('.multi-language-field'), language = $(this).data('language');

		parent.find('.current-language').removeClass('current-language');
		parent.find('[data-language="' + language + '"]').addClass('current-language');
		parent.find('input[data-language="' + language + '"], textarea[data-language="' + language + '"]').focus();
	});

	/**
	 * Focus/blur fields.
	 */
	$body.on('focusin', '.multi-language-field input, .multi-language-field textarea', function() {
		$(this).parent('.multi-language-field').addClass('focused');
	});

	$body.on('focusout', '.multi-language-field input, .multi-language-field textarea', function() {
		$(this).parent('.multi-language-field').removeClass('focused');
	});

	/**
	 * Keep the selected editor in sync across languages.
	 */
	$body.on('click', '.wp-editor-tabs .wp-switch-editor', function() {
		var parent = $(this).parents('.multi-language-field'), editor = $(this).hasClass('switch-tmce') ? 'tmce' : 'html';
		parent.find('.wp-editor-tabs .wp-switch-editor.switch-' + editor).not(this).each(function() {
			switchEditors.switchto(this);
		});
	});


	/**
	 * Handle qTranslate-X integrations after jQuery.ready()
	 */
	$(window).load(function() {
		// only proceed if qTranslate is loaded
		if (!qTranslateConfig || !qTranslateConfig.qtx) {
			return;
		}

		// Add display hooks to ACF metaboxes
		$('.acf_postbox h3 span, .acf-postbox h3 span').each(function() {
			this.id = _.uniqueId('acf-postbox-h3-span');
			qTranslateConfig.qtx.addDisplayHookById(this.id);
		});
	});

});