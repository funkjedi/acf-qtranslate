=== ACF qTranslate ===
Contributors: funkjedi
Tags: acf, advanced custom fields, qtranslate, add-on, admin
Requires at least: 3.0.0
Tested up to: 3.7.1
Version: 1.3
Stable tag: 1.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds qTranslate compatible fields for Text, Text Area, Wysiwyg Editor and Image.

== Description ==

**Requires the following plugins**

* [Advanced Custom Fields](http://wordpress.org/extend/plugins/advanced-custom-fields/)
* [qTranslate](http://wordpress.org/plugins/qtranslate/)

This plugin adds a new Field Type category called qTranslate. This contains qTranslate compatible fields for Text, Text Area, Wysiwyg Editor and Image.

Usage:

* Install the plug-in (standard WordPress installation)
* Set up a qTranslate field (standard Advanced Custom Field configuration)
* Add or Edit a post and language tabs will show up above the qTranslate field
* Click a language tab above the qTranslate field to switch languages

Issues:

All bugs reports or feature requests should be made in the [Github repository](https://github.com/funkjedi/acf-qtranslate/issues)

== Installation ==

1. Be sure to have **Advanced Custom Fields** installed and activated first
2. Upload `acf-qtranslate` directory to the `/wp-content/plugins/` directory
3. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= What's the history behind this plugin? =

The plugin is based on code samples posted to the [ACF support forums](http://old.support.advancedcustomfields.com/discussion/comment/20351#Comment_20351) by taeo.

== Screenshots ==

1. Shows the qTranslate Text and Image fields.

== Changelog ==

= 1.3 =
Updated styles for Wordpress 3.8
Updated monkey patch qTranslate to fix [bug with multiple WYSIWYG editors](https://github.com/funkjedi/acf-qtranslate/issues/2#issuecomment-37612918)

= 1.2 =
Monkey patch qTranslate to fix [bug with multiple WYSIWYG editors](http://www.qianqin.de/qtranslate/forum/viewtopic.php?f=3&t=3497).

= 1.1 =
Added support for Image Fields. Thanks to bookwyrm for the contribution.

= 1.0 =
Initial Release. Thanks to taeo for the code samples this plugin was based on.
