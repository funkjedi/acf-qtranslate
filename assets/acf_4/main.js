
/**
 * Handle qTranslate-X integrations after jQuery.ready()
 */
jQuery(function($) {
	$(window).load(function() {

		// only proceed if qTranslate is loaded
		if (!qTranslateConfig || !qTranslateConfig.qtx) {
			return;
		}

		// Add display hooks to ACF metaboxes
		$('.acf_postbox h3 span').each(function() {
			this.id = _.uniqueId('acf-postbox-h3-span');
			qTranslateConfig.qtx.addDisplayHookById(this.id);
		});

		// Selectors for supported field types
		var field_types = [
			'.field_type-text input:text',
			'.field_type-textarea textarea',
			'.field_type-wysiwyg .wp-editor-area'
		].join(',');

		// Remove content hooks from ACF Repeater and Flexible Fields clones
		$('.row-clone .qtranxs-translatable').each(function() {
			qTranslateConfig.qtx.removeContentHook(this);
		});

		// Add content hooks for existing fields
		$('.field_type-repeater .row, .field_type-flexible_content .row').each(function() {
			var row = $(this);
			row.find(field_types).not('.qtranxs-translatable').each(function() {
					qTranslateConfig.qtx.addContentHookC(this, row.closest('form').get(0));
			});
		})

		// Watch and add content hooks when new fields are added
		$(document).on('acf/setup_fields', function(e, new_field) {
			new_field = $(new_field);
			if (new_field.hasClass('row')) {
				new_field.find(field_types).not('.qtranxs-translatable').each(function() {
					qTranslateConfig.qtx.addContentHookC(this, new_field.closest('form').get(0));
				});
			}
		});

		// Watch and remove content hooks when fields are removed
		$('body').on('click', '.row .acf-button-remove', function() {
			var row = $(this).closest('.row');
			row.find(field_types).filter('.qtranxs-translatable').each(function() {
				qTranslateConfig.qtx.removeContentHook(this);
			});
		});

	});
});


