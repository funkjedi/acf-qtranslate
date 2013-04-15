
jQuery(function($) {


	$('body').on('click', '.wp-switch-editor[data-language]', function() {
		var parent = $(this).parent('.multi-language-field'), language = $(this).data('language');

		parent.find('.current-language').removeClass('current-language');
		parent.find('[data-language="' + language + '"]').addClass('current-language');
		parent.find('input[data-language="' + language + '"], textarea[data-language="' + language + '"]').focus();
	});

	$('body').on('focusin', '.multi-language-field input, .multi-language-field textarea', function() {
		$(this).parent('.multi-language-field').addClass('focused');
	});

	$('body').on('focusout', '.multi-language-field input, .multi-language-field textarea', function() {
		$(this).parent('.multi-language-field').removeClass('focused');
	});


});
