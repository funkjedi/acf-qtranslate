
/**
 * Clone functionality from standard Image field type
 */
acf.fields.qtranslate_image = acf.fields.image.extend({
	type: 'qtranslate_image',
	focus: function() {
		this.$el = this.$field.find('.acf-image-uploader.current-language');
		this.o = acf.get_data(this.$el);
	}
});

/**
 * Clone functionality from standard File field type
 */
acf.fields.qtranslate_file = acf.fields.file.extend({
	type: 'qtranslate_file',
	focus: function() {
		this.$el = this.$field.find('.acf-file-uploader.current-language');
		this.o = acf.get_data(this.$el);
	}
});

/**
 * Clone functionality from standard WYSIWYG field type
 */
acf.fields.qtranslate_wysiwyg = acf.fields.wysiwyg.extend({
	type: 'qtranslate_wysiwyg',
	focus: function() {
		this.$el = this.$field.find('.wp-editor-wrap.current-language').last();
		this.$textarea = this.$el.find('textarea');
		this.o = acf.get_data(this.$el);
		this.o.id = this.$textarea.attr('id');
	},
	initialize: function() {
		var self = this;
		this.$field.find('.wp-editor-wrap').each(function() {
			self.$el = jQuery(this);
			self.$textarea = self.$el.find('textarea');
			self.o = acf.get_data(self.$el);
			self.o.id = self.$textarea.attr('id');
			acf.fields.wysiwyg.initialize.call(self);
		});
		this.focus();
	}
});



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
		$('.acf-postbox h3 span').each(function() {
			this.id = _.uniqueId('acf-postbox-h3-span');
			qTranslateConfig.qtx.addDisplayHookById(this.id);
		});

		// Selectors for supported field types
		var field_types = {
			repeater:         'input:hidden',
			flexible_content: 'input:hidden',
			text:             'input:text',
			textarea:         'textarea',
			wysiwyg:          '.wp-editor-area',
		};

		// Remove content hooks from ACF Repeater and Flexible Fields clones
		$('.acf-clone .wp-editor-area.qtranxs-translatable').each(function() {
			qTranslateConfig.qtx.removeContentHook(this);
		});

		// Setup field types
		$.each(field_types, function(field_type, selector) {

			// Add content hooks for existing fields
			acf.get_fields({ type: field_type }).each(function() {
				var form = $(this).closest('form').get(0);
				var field = $(this).find(selector).get(0);
				qTranslateConfig.qtx.addContentHookC(field, form);
			});

			// Watch and add content hooks when new fields are added
			acf.add_action('append_field/type=' + field_type, function($el) {
				var form = $el.closest('form').get(0);
				var field = $el.find(selector).get(0);
				qTranslateConfig.qtx.addContentHookC(field, form);
				// Run at higher integer priority than the default in case the ACF handlers
				// change the id of the underlying input
			}, 100);

		});

		// Watch and remove content hooks when fields are removed
		// however ACF removes the elements from the DOM early so
		// we must hook into handler and perform updates there
		var _hooked_repeater_remove = acf.fields.repeater.remove;
		acf.fields.repeater.remove = function(event) {
			var row = event.$el.closest('.acf-row');
			row.find(_.toArray(field_types).join(',')).filter('.qtranxs-translatable').each(function() {
				qTranslateConfig.qtx.removeContentHook(this);
			});
			// call the original handler
			_hooked_repeater_remove.call(this, event);
		}

	});
});

