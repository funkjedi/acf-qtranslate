
jQuery(window).load(function() {

	// Only proceed if qTranslate is loaded
	if (typeof qTranslateConfig != 'object' || typeof qTranslateConfig.qtx != 'object') {
		return;
	}

	// Enable the language switching buttons
	qTranslateConfig.qtx.enableLanguageSwitchingButtons('block');

	// Add display hooks to ACF metaboxes
	jQuery('.acf_postbox h3 span').each(function() {
		this.id = _.uniqueId('acf-postbox-h3-span');
		qTranslateConfig.qtx.addDisplayHookById(this.id);
	});


	// Ensure that translation of standard field types is enabled
	if (!window.acf_qtranslate_translate_standard_field_types) {
		return;
	}

	// Selectors for supported field types
	var field_types = [
		'.field_type-text input:text',
		'.field_type-textarea textarea',
		'.field_type-wysiwyg .wp-editor-area'
	].join(',');

	// Remove content hooks from ACF Fields
	jQuery('.acf_postbox .field').find('.qtranxs-translatable').each(function() {
		qTranslateConfig.qtx.removeContentHook(this);
	});

	// Watch and add content hooks when new fields are added
	var timeoutContentHooksTinyMCE;
	jQuery(document).on('acf/setup_fields', function(e, new_field) {
		new_field = jQuery(new_field);
		new_field.find(field_types).not('.qtranxs-translatable').each(function() {
			var field = jQuery(this);

			// Skip over fields inside of ACF Repeater
			// and Flexible Content clone rows
			if (field.parents('.row-clone').length) {
				return;
			}

			qTranslateConfig.qtx.addContentHookC(this, field.closest('form').get(0));

			// Since ACFv4 doesn't update tinyMCEPreInit.mceInit so we
			// need to manully set it so that the translation hooks apply properly
			if (field.hasClass('wp-editor-area')) {
				if (typeof tinyMCEPreInit.mceInit[this.id] == 'undefined') {
					var mceInit = jQuery.extend({}, tinyMCEPreInit.mceInit.acf_settings);
					mceInit.id = this.id;
					tinyMCEPreInit.mceInit[this.id] = mceInit;
				}
			}
		});

		// Run in a setTimeout block to give the tinyMCE instance
		// enough time to initialize before setting the editor hooks
		clearTimeout(timeoutContentHooksTinyMCE);
		timeoutContentHooksTinyMCE = setTimeout(function(){
			qTranslateConfig.qtx.addContentHooksTinyMCE();
			jQuery.each(tinyMCE.editors, function(i, ed){
				var mceInit = tinyMCEPreInit.mceInit[ed.id];
					console.log('initEditors:',mceInit);
				if (mceInit && mceInit.init_instance_callback) {
					mceInit.init_instance_callback(ed);
				}
			});
		}, 50);
	});

	// Watch and remove content hooks when fields are removed
	jQuery('body').on('click', '.row .acf-button-remove', function() {
		var row = jQuery(this).closest('.row');
		row.find(field_types).filter('.qtranxs-translatable').each(function() {
			qTranslateConfig.qtx.removeContentHook(this);
		});
	});

});
