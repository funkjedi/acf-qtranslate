
/**
 * Clone functionality from standard Image field type
 */
acf.fields.qtranslate_image = acf.fields.image.extend({
    type: 'qtranslate_image',
    focus: function() {
        this.$el = this.$field.find('.acf-image-uploader.current-language');
        this.$input = this.$el.find('input[type="hidden"]');
        this.$img = this.$el.find('img');

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
        this.$input = this.$el.find('input[type="hidden"]');

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
