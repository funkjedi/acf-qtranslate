
jQuery(function($) {

	var $body = $('body');


	/**
	 * Sync qtranslate language switchers with qtranslatex language switchers.
	 */
	$body.on('click', '.qtranxs-lang-switch', function() {
		var parent = $('.multi-language-field'), language = $(this).attr('lang');

		parent.find('.current-language').removeClass('current-language');
		parent.find('[data-language="' + language + '"]').addClass('current-language');
		parent.find('input[data-language="' + language + '"], textarea[data-language="' + language + '"]').focus();
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


});
