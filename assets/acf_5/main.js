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

acf.registerFieldType(acf.models.UrlField.extend({
  type: 'qtranslate_url',
  $control: function(){
    return this.$('.acf-input-wrap.current-language');
  },

  $input: function(){
    return this.$('.acf-input-wrap.current-language input[type="url"]');
  },
  isValid: function(){

    // vars
    var val = this.val();

    // bail early if no val
    if( !val ) {
      return false;
    }

    // url
    if( val.indexOf('://') !== -1 ) {
      return true;
    }

    // protocol relative url
    if( val.indexOf('//') === 0 ) {
      return true;
    }

    // relative url
    if( val.indexOf('/') === 0 ) {
      return true;
    }

    // return
    return false;
  },

  render: function(){

    // add class
    if( this.isValid() ) {
      this.$control().addClass('-valid');
    } else {
      this.$control().removeClass('-valid');
    }
  },
}));