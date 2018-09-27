/**
 * Clone functionality from standard Image field type
 */
acf.registerFieldType(acf.models.ImageField.extend({
  type: 'qtranslate_image',

  $control: function(){
    return this.$('.acf-image-uploader.current-language');
  },

  $input: function(){
    return this.$('.acf-image-uploader.current-language input[type="hidden"]');
  },

  render: function( attachment ){
    var control = this.$control();

    // vars
    attachment = this.validateAttachment( attachment );

    // update image
    control.find('img').attr({
      src: attachment.url,
      alt: attachment.alt,
      title: attachment.title
    });

    // vars
    var val = attachment.id || '';

    // update val
    this.val( val );

    // update class
    if( val ) {
      control.addClass('has-value');
    } else {
      control.removeClass('has-value');
    }
  }
}));

/**
 * Clone functionality from standard File field type
 */
acf.registerFieldType(acf.models.QtranslateImageField.extend({
  type: 'qtranslate_file',
  render: function( attachment ){
    var control = this.$control();
    
    // vars
    attachment = this.validateAttachment( attachment );

    // update image
    this.$control().find(' img').attr({
      src: attachment.icon,
      alt: attachment.alt,
      title: attachment.title
    });

    // update elements
    control.find('[data-name="title"]').text( attachment.title );
    control.find('[data-name="filename"]').text( attachment.filename ).attr( 'href', attachment.url );
    control.find('[data-name="filesize"]').text( attachment.filesizeHumanReadable );

    // vars
    var val = attachment.id || '';

    // update val
    acf.val( this.$input(), val );

    // update class
    if( val ) {
      control.addClass('has-value');
    } else {
      control.removeClass('has-value');
    }
  },
}));

acf.registerFieldType(acf.models.WysiwygField.extend({
  type: 'qtranslate_wysiwyg',
  initializeEditor: function() {
    var self = this;
    this.$('.acf-editor-wrap').each(function() {
      var $wrap = $(this);
      var $textarea = $wrap.find('textarea');
      var args = {
        tinymce: true,
        quicktags: true,
        toolbar: self.get('toolbar'),
        mode: self.getMode(),
        field: self
      };

      // generate new id
      var oldId = $textarea.attr('id');
      var newId = acf.uniqueId('acf-editor-');

      // rename
      acf.rename({
        target: $wrap,
        search: oldId,
        replace: newId,
        destructive: true
      });

      // update id
      self.set('id', newId, true);

      // initialize
      acf.tinymce.initialize(newId, args);
    });
  }
}));
