
jQuery(function($) {


	$('body').on('click', '.wp-switch-editor[data-language]', function() {
		var parent = $(this).parent('.multi-language-field'), language = $(this).data('language');

		parent.find('.current-language').removeClass('current-language');
		parent.find('[data-language="' + language + '"]').addClass('current-language');
		parent.find('input[data-language="' + language + '"], textarea[data-language="' + language + '"]').focus();

		// if a TinyMCE hasn't been initalize for this language then initalize it
		if (!parent.find('.current-language .mce-tinymce').length) {
			acf.do_action('ready', parent.parents('.acf-field'));
		}
	});

	$('body').on('focusin', '.multi-language-field input, .multi-language-field textarea', function() {
		$(this).parent('.multi-language-field').addClass('focused');
	});

	$('body').on('focusout', '.multi-language-field input, .multi-language-field textarea', function() {
		$(this).parent('.multi-language-field').removeClass('focused');
	});


});



(function($){

	acf.fields.qtranslate_wysiwyg = acf.field.extend({

		type: 'qtranslate_wysiwyg',
		$el: null,
		$textarea: null,
		toolbars: {},

		actions: {
			'ready':		'initialize',
			'append':		'initialize',
			'remove':		'disable',
			'sortstart':	'disable',
			'sortstop':		'enable'
		},

		focus: function(){

			// update vars
			this.$el = this.$field.find('.wp-editor-wrap.current-language').last();
			this.$textarea = this.$el.find('textarea');


			// settings
			this.settings = acf.get_data( this.$el );
			this.settings.id = this.$textarea.attr('id');
		},

		initialize: function(){

			// bail early if no tinymce
			if( typeof tinyMCEPreInit === 'undefined' ) {

				return false;

			}


			// vars
			var mceInit = this.get_mceInit(),
				qtInit = this.get_qtInit();


			// append settings
			tinyMCEPreInit.mceInit[ mceInit.id ] = mceInit;
			tinyMCEPreInit.qtInit[ qtInit.id ] = qtInit;


			// initialize mceInit
			if( this.$el.hasClass('tmce-active') ) {

				try {

					tinymce.init( mceInit );

				} catch(e){}

			}


			// initialize qtInit
			try {

				var qtag = quicktags( qtInit );

				this._buttonsInit( qtag );

			} catch(e){}

		},


		get_mceInit : function(){

			// reference
			var $field = this.$field;


			// vars
			var toolbar = this.get_toolbar( this.settings.toolbar ),
				mceInit = $.extend({}, tinyMCEPreInit.mceInit.acf_content);


			// selector
			mceInit.selector = '#' + this.settings.id;


			// id
			mceInit.id = this.settings.id; // tinymce v4
			mceInit.elements = this.settings.id; // tinymce v3


			// toolbar
			if( toolbar ) {

				var k = (tinymce.majorVersion < 4) ? 'theme_advanced_buttons' : 'toolbar';

				for( var i = 1; i < 5; i++ ) {

					mceInit[ k + i ] = acf.isset(toolbar, i) ? toolbar[i] : '';

				}

			}


			// events
			if( tinymce.majorVersion < 4 ) {

				mceInit.setup = function( ed ){

					ed.onInit.add(function(ed, event) {

						// focus
						$(ed.getBody()).on('focus', function(){

							acf.validation.remove_error( $field );

						});

						$(ed.getBody()).on('blur', function(){

							// update the hidden textarea
							// - This fixes a bug when adding a taxonomy term as the form is not posted and the hidden textarea is never populated!

							// save to textarea
							ed.save();


							// trigger change on textarea
							$field.find('textarea').trigger('change');

						});

					});

				};

			} else {

				mceInit.setup = function( ed ){

					ed.on('focus', function(e) {

						acf.validation.remove_error( $field );

					});

					ed.on('blur', function(e) {

						// update the hidden textarea
						// - This fixes a but when adding a taxonomy term as the form is not posted and the hidden textarea is never populated!

						// save to textarea
						ed.save();


						// trigger change on textarea
						$field.find('textarea').trigger('change');

					});

				};

			}


			// hook for 3rd party customization
			mceInit = acf.apply_filters('wysiwyg_tinymce_settings', mceInit, mceInit.id);


			// return
			return mceInit;

		},

		get_qtInit : function(){

			// vars
			var qtInit = $.extend({}, tinyMCEPreInit.qtInit.acf_content);


			// id
			qtInit.id = this.settings.id;


			// hook for 3rd party customization
			qtInit = acf.apply_filters('wysiwyg_quicktags_settings', qtInit, qtInit.id);


			// return
			return qtInit;

		},

		/*
		*  disable
		*
		*  This function will disable the tinymce for a given field
		*  Note: txtarea_el is different from $textarea.val() and is the value that you see, not the value that you save.
		*        this allows text like <--more--> to wok instead of showing as an image when the tinymce is removed
		*
		*  @type	function
		*  @date	1/08/2014
		*  @since	5.0.0
		*
		*  @param	n/a
		*  @return	n/a
		*/

		disable: function(){

			try {

				// vars
				var ed = tinyMCE.get( this.settings.id ),
					txtarea_el = tinyMCE.DOM.get( this.settings.id );
					val = txtarea_el.value;


				// destory
				ed.destroy();


				// update value
				if( this.$field.find('.wp-editor-wrap').hasClass('html-active') ) {

					txtarea_el.value = val;

				}


			} catch(e) {}

		},

		enable: function(){

			// bail early if html mode
			if( this.$field.find('.wp-editor-wrap').hasClass('html-active') ) {

				return;

			}


			try {

				tinyMCE.init( tinyMCEPreInit.mceInit[ this.settings.id ] );

			} catch(e) {}


		},

		get_toolbar : function( name ){

			// bail early if toolbar doesn't exist
			if( typeof this.toolbars[ name ] !== 'undefined' ) {

				return this.toolbars[ name ];

			}


			// return
			return false;

		},


		/*
		*  _buttonsInit
		*
		*  This function will add the quicktags HTML to a WYSIWYG field. Normaly, this is added via quicktags on document ready,
		*  however, there is no support for 'append'. Source: wp-includes/js/quicktags.js:245
		*
		*  @type	function
		*  @date	1/08/2014
		*  @since	5.0.0
		*
		*  @param	ed (object) quicktag object
		*  @return	n/a
		*/

		_buttonsInit: function( ed ) {
			var defaults = ',strong,em,link,block,del,ins,img,ul,ol,li,code,more,close,';

			canvas = ed.canvas;
			name = ed.name;
			settings = ed.settings;
			html = '';
			theButtons = {};
			use = '';

			// set buttons
			if ( settings.buttons ) {
				use = ','+settings.buttons+',';
			}

			for ( i in edButtons ) {
				if ( !edButtons[i] ) {
					continue;
				}

				id = edButtons[i].id;
				if ( use && defaults.indexOf( ',' + id + ',' ) !== -1 && use.indexOf( ',' + id + ',' ) === -1 ) {
					continue;
				}

				if ( !edButtons[i].instance || edButtons[i].instance === inst ) {
					theButtons[id] = edButtons[i];

					if ( edButtons[i].html ) {
						html += edButtons[i].html(name + '_');
					}
				}
			}

			if ( use && use.indexOf(',fullscreen,') !== -1 ) {
				theButtons.fullscreen = new qt.FullscreenButton();
				html += theButtons.fullscreen.html(name + '_');
			}


			if ( 'rtl' === document.getElementsByTagName('html')[0].dir ) {
				theButtons.textdirection = new qt.TextDirectionButton();
				html += theButtons.textdirection.html(name + '_');
			}

			ed.toolbar.innerHTML = html;
			ed.theButtons = theButtons;

		},

	});

})(jQuery);

