=== Recras ===
Contributors: zanderz
Tags: recras, recreation, reservation, booking, voucher
Tested up to: 6.9
Stable tag: 6.5.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily integrate data from your Recras instance, such as packages and contact forms, into your own website.

== Description ==
With this plugin, you can easily integrate data from your [Recras](https://recras.nl/) instance, such as packages and contact forms, into your own website.

To get started, go to the Recras → Settings page and enter your Recras domain. For example, if you log in to Recras at `https://mycompany.recras.com/` then enter `mycompany.recras.com`. That's all there is to it! You can now use widgets to retrieve data. All data is retrieved via a secured connection (HTTPS) to ensure data integrity. Other than the request parameters, no data is sent to the Recras servers.

This plugin consists of the following "widgets". To use them, you first need to set your Recras domain (see paragraph above).
* Book processes
* Contact forms
* Packages
* Products
* Voucher info

Widgets can be added to your site in three ways. Using Gutenberg blocks (recommended), using the buttons in the "classic editor" (limited functionality), or by entering the shortcode manually (discouraged).

= Date picker for contact forms =
By default, date pickers in contact forms use the browser date picker. If you want to be able to style the date picker, we recommend to enable the date picker we have included with the plugin. You can enable this on the Recras → Settings page.

**Note**: this setting only applies to standalone contact forms, not to contact forms used in the old online booking of packages integration or in book processes.

= Styling =
No custom styling is applied by default, so it will integrate with your site easily. If you want to apply custom styling, see `css/style.css` for all available classes. Be sure to include these styles in your own theme, this stylesheet is not loaded by the plugin!
For styling the date picker, we refer you to the [Pikaday repository](https://github.com/Pikaday/Pikaday). Be sure to make any changes in your own theme or using WordPress' own Customizer.

= Cache =
All data from your Recras is cached for up to 24 hours. If you make important changes, such as increasing the price of a product, you can clear the cache to reflect those changes on your site immediately.

= Google Analytics integration =
You can enable basic Google Analytics integration for the booking of packages and voucher sales by checking "Enable Google Analytics integration?" on the Recras Settings page. This will only work if there is a global `ga` JavaScript object. This should almost always be the case, but if you find out it doesn't work, please contact us!

== Installation ==

**Easy installation (preferred)**

1. Install the plugin from the Plugins > Add New page in your WordPress installation.

**Self install**

1. Download the zip file containing the plugin and extract it somewhere to your hard drive
1. Upload the `recras-wordpress-plugin` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress

**Using Composer**

1. Type `composer require recras/recras-wordpress-plugin` in your terminal
1. The plugin will automatically be installed in the `/wp-content/plugins/` directory by using Composer Installers
1. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= Do you support Elementor, WPBakery/Visual Composer, Brizy, etc. ? =
Integrating a book process is possible through Elementor. There are many different page builders but Elementor is the most used one, so we chose to only build this widget for Elementor. Using shortcodes is still possible in all other builders, of course.

= Does the plugin support network installations? =
Yes it does. You can set different Recras domains (all settings, for that matter) for each site.

= Can the plugin be installed as "must use plugin" ? =
No. "Must use" plugins don't appear in the update notifications nor show their update status on the plugins page (direct quote from the <a href="https://wordpress.org/support/article/must-use-plugins/">WordPress documentation</a>) which is reason enough for us not to support this.

== Screenshots ==

1. Example of a programme with the Twenty Fifteen theme
2. Example of package information, generated from Recras data
3. The Recras blocks in Gutenberg

== Changelog ==

= 6.5.1 =
* Gutenberg editor: Add ID to book processes list
* Gutenberg editor: sort selectable items by name instead of creation date

= 6.5.0 =
* Add anti-spam check to contact forms

= 6.4.3 =
* Fixed error when using an invalid Recras domain
* Updated translations

= 6.4.2 =
* Update "Fix book process datepicker styling" styles to latest version
* Fixed some WP compliance issues

= 6.4.1 =
* Don't load old script when it's not needed
* Updated "Tested up to" version to 6.9
* Update "Fix book process datepicker styling" styles to latest version

= 6.4.0 =
* Instead of entering your Recras subdomain in the settings, you must now enter the full URL. This is done since Recras members from now on get a subdomain at recras.com instead of recras.nl.
* Plugin now requires WP 6.7 or higher

= 6.3.8 =
* Update "Fix book process datepicker styling" styles to latest version
* Small technical update

= 6.3.7 =
* Show error on settings page if subdomain appears invalid
* Add caching hint to book process script

= 6.3.6 =
* Fix errors when using WP CLI for certain things

= 6.3.5 =
* Fix warning when using PHP 8.4

= 6.3.4 =
* Improve choosing a start time when displaying package programme
* Fix display of voucher templates with a validity set to a certain date, instead of a number of days
* Load book process script differently
* Updated "Tested up to" version to 6.8
* Plugin now requires WP 6.6 or higher

= 6.3.3 =
* Update "Fix book process datepicker styling" styles to latest version
* Remove outdated documentation

= 6.3.2 =
* Hide "Package availability" widget when it's not available in your Recras instance
* "Package availability" widget: hide packages where availability API is disabled

= 6.3.1 =
* Fix console message

= 6.3.0 =
* The plugin now hides certain blocks/editor buttons, when they're not available in your Recras instance
* Plugin now requires WP 6.5 or higher

= Older versions =
See [the full changelog](https://github.com/Recras/recras-wordpress-plugin/blob/master/changelog.md) for older versions.

== Upgrade Notice ==
See changelog. We use semantic versioning for the plugin.

== Support ==
We would appreciate it if you use [our GitHub page](https://github.com/Recras/recras-wordpress-plugin/issues) for bug reports, pull requests and general questions. If you do not have a GitHub account, you can use the Support forum on wordpress.org.

We only support the latest plugin of the plugin, on the latest version of WordPress (which you should always use anyway!) and only on [actively supported PHP branches](https://www.php.net/supported-versions.php).

== Credits ==
* Icons from [Dashicons](https://github.com/WordPress/dashicons) by WordPress, released under the GPLv2 licence.
* Date picker is [Pikaday](https://github.com/Pikaday/Pikaday), released under the BSD/MIT licence.
* Country list is by [umpirsky](https://github.com/umpirsky/country-list), released under the MIT licence.
