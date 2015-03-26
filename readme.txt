=== ACF qTranslate ===
Contributors: funkjedi
Tags: acf, advanced custom fields, qtranslate, add-on, admin
Requires at least: 3.5.0
Tested up to: 4.1.1
Version: 1.7.6
Stable tag: 1.7.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Provides qTranslate compatible ACF field types for Text, Text Area, WYSIWYG, Image and File.


== Description ==

This plugin provides qTranslate (qTranslate-X, qTranslate Plus and mqTranslate) compatible ACF4 and ACF5PRO field types for Text, Text Area, WYSIWYG, Image and File. When adding a field to a field group these new field types will be listed under the qTranslate category in the Field Type dropdown.

= Field Types =
* qTranslate Text (type text, api returns text)
* qTranslate Text Area (type text, api returns text)
* qTranslate WYSIWYG (a wordpress wysiwyg editor, api returns html)
* qTranslate Image (upload an image, api returns the url)
* qTranslate File (upload a file, api returns the url)

= qTranslate-X =
If using qTranslate-X the standard Text, Text Area and WYSIWYG field types all automatically support translation out of the box.

= Bug Submission =
https://github.com/funkjedi/acf-qtranslate/issues/


== Installation ==

1. Upload `acf-qtranslate` directory to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

= Requires ACF4 or ACF5PRO =
* [ACF](https://wordpress.org/plugins/advanced-custom-fields/)
* [ACF5PRO](http://www.advancedcustomfields.com/pro/)

= Requires qTranslate (or qTranslate-based) Plugin =
* [qTranslate](https://wordpress.org/extend/plugins/qtranslate/)
* [qTranslate-X](https://wordpress.org/plugins/qtranslate-x/)
* [qTranslate Plus](https://wordpress.org/plugins/qtranslate-xp/)
* [mqTranslate](https://wordpress.org/plugins/mqtranslate/)
* [zTranslate](https://wordpress.org/extend/plugins/ztranslate/)


== Frequently Asked Questions ==

= What's the history behind this plugin? =
The plugin is based on code samples posted to the ACF support forums by taeo back in 2013.


== Screenshots ==

1. Shows the qTranslate Text and Image fields.


== Changelog ==

= 1.7.6 =
* Core: qTranslate-X support for Text, Text Area and WYSIWYG inside repeater
* Bug Fix: Display qTranslate-X switcher for qTranslate Field Types
* Bug Fix: Incorrectly loading in Media Library and Widgets screens

= 1.7.5 =
* Core: Updates to README file
* Bug Fix: Updated to visible ACF fields detection

= 1.7.4 =
* Bug Fix: Only load admin javascript when there are visible ACF fields

= 1.7.3 =
* Core: Removed namespaces to make code compatible with PHP 5.2

= 1.7.2 =
* Bug Fix: Corrected misnamed variable
* Bug Fix: ACF5 issues using WYSIWYG with the repeater field type
* Bug Fix: qTranslate-X saving content using WYSIWYG with repeater field type
* Core: Support for `qtrans_edit_language` cookie set by qTranslate-X
* Core: Keep switches between Visual/Html modes in sync across languages

= 1.7.1 =
* Core: Added back ACF5 support for WYSIWYG
* Core: Added qTranslate-X support for the standard WYSIWYG field type
* Core: Bumped version requirement to match ACF
* Bug Fix: qTranslate-X switcher showing up on every admin page

= 1.7 =
* Core: Refactor of codebase
* Core: Support for qTranslate-X language switchers

= 1.6 =
* Core: Added ACFv4 support for qTranslate-X

= 1.5 =
* Core: Added compatibility for qTranslate-X
* Bug Fix: Remove the broken ACF5 WYSIWYG implementation

= 1.4 =
* Core: Added support for ACF5
* Core: Tested compatibility with mqTranslate

= 1.3 =
* Core: Updated styles for Wordpress 3.8
* Bug Fix: qTranslate bug with multiple WYSIWYG editors

= 1.2 =
* Bug Fix: qTranslate bug with multiple WYSIWYG editors

= 1.1 =
* Core: Added support for Image Fields. Thanks to bookwyrm for the contribution.

= 1.0 =
* Initial Release. Thanks to taeo for the code samples this plugin was based on.


== Upgrade Notice ==

= 1.7.3 =
Removed namespaces to make code compatible with PHP 5.2
