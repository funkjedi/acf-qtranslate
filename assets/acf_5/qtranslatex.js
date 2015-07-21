
(function(){

	acf.qtx = acf.qtx || {};

	acf.qtx.image = function( field ) {
		this.$field = jQuery(field);
		this.$field_container = this.$field.closest('.acf-image-uploader');
		this.id = this.$field.val();
		this.needs_render = false;
		return this;
	};

	// Check if image needs updating and prepare attachment
	acf.qtx.image.prototype.prepare = function() {
		// Check if attachment is shown and matches current value
		if ( this.$field_container.hasClass('has-value') && this.id === this.$field.val() ) {
			this.needs_render = false;
			return this;
		}
		this.needs_render = true;
		// Check if image was removed
		if ( '' === this.$field.val() ) {
			this.attachment = {};
			return this;
		}
		this.attachment = wp.media.model.Attachment.get( this.$field.val() );
		return this;
	};

	// Remove image on language switch if val is empty
	acf.qtx.image.prototype.remove = function() {
		this.$field_container.find('img').attr('src', '' );
		this.$field_container.removeClass('has-value');
		this.$field_container.trigger('change');
		this.needs_render = false;
		return this;
	}

	// Render image html
	acf.qtx.image.prototype.render = function() {
		// Called inappropriately
		if ( !this.needs_render ) {
			return this;
		}
		if ( '' === this.$field.val() ) {
			return this.remove();
		}
		// Have to do this because inside the success context, this = window
		var $this = this;
		this.attachment.fetch({
			success: function( att ) {
				// Parse attachment for image arguments
				image = acf.fields.image.prepare( att );
				$this.$field_container.find('img').attr('src', image.url );
				$this.$field_container.addClass('has-value');
				$this.$field_container.trigger('change');
				// Update internal id
				$this.id = $this.$field.val();
				$this.needs_render = false;
			}
		});
		return this;
	}

	// Convenience function for adding language switch handler
	acf.qtx.image.prototype.watch = function() {
		var image = this;
		qTranslateConfig.qtx.addLanguageSwitchAfterListener( function() {
			console.log('hook');
			image.prepare().render();
		});
	}

	var windowLoadCompleted = false;
	jQuery(window).load(function() {

		// Prevent from being triggered again
		if (windowLoadCompleted) {
			return;
		}

		windowLoadCompleted = true;

		// Only proceed if qTranslate is loaded
		if (typeof qTranslateConfig != 'object' || typeof qTranslateConfig.qtx != 'object') {
			return;
		}

		// Enable the language switching buttons
		qTranslateConfig.qtx.enableLanguageSwitchingButtons('block');

		// Add display hooks to ACF metaboxes
		jQuery('.acf-postbox h3 span').each(function() {
			this.id = _.uniqueId('acf-postbox-h3-span');
			qTranslateConfig.qtx.addDisplayHookById(this.id);
		});


		// Ensure that translation of standard field types is enabled
		if (!window.acf_qtranslate_translate_standard_field_types) {
			return;
		}

		// Selectors for supported field types
		var field_types = {
			text:      'input:text',
			textarea:  'textarea',
			wysiwyg:   '.wp-editor-area',
			image:     '.acf-image-uploader input',
		};

		// Remove content hooks from ACF Fields
		jQuery('.acf-postbox .acf-field').find('.qtranxs-translatable').each(function() {
			qTranslateConfig.qtx.removeContentHook(this);
		});

		// Setup field types
		jQuery.each(field_types, function(field_type, selector) {

			// Add content hooks for existing fields
			acf.get_fields({ type: field_type }).each(function() {
				var form = jQuery(this).closest('form').get(0);
				var field = jQuery(this).find(selector).get(0);
				qTranslateConfig.qtx.addContentHookC(field, form);

				if ('image' === field_type ) {
					image = new acf.qtx.image( field );
					image.prepare().render().watch();
				}
			});

			// Watch and add content hooks when new fields are added
			acf.add_action('append_field/type=' + field_type, function($el) {
				var form = $el.closest('form').get(0);
				var field = $el.find(selector).get(0);
				qTranslateConfig.qtx.addContentHookC(field, form);

				if (jQuery(field).hasClass('wp-editor-area')) {
					qTranslateConfig.qtx.addContentHooksTinyMCE();

					// We must manually trigger load event so that the
					// loadTinyMceHooks function which calls setEditorHooks is executed
					var loadEvent = document.createEvent('UIEvents');
					loadEvent.initEvent('load',false,false,window);
					window.dispatchEvent(loadEvent);
				}

				// Run at higher integer priority than the default in case the ACF handlers
				// change the id of the underlying input
			}, 100);

		});

		qTranslateConfig.qtx.addContentHooksTinyMCE();

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
		};

	});

})();
