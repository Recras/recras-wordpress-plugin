=== Recras WordPress plugin ===
Contributors: zanderz
Tags: recras, recreation, reservation, booking, voucher
Tested up to: 6.4
Stable tag: 6.0.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easily integrate data from your Recras instance, such as packages and contact forms, into your own website.

== Description ==
With this plugin, you can easily integrate data from your [Recras](https://recras.nl/) instance, such as packages and contact forms, into your own website.

To get started, go to the Recras → Settings page and enter your Recras name. For example, if you log in to Recras at `https://mysite.recras.nl/` then your Recras name is `mysite`. That's all there is to it! You can now use widgets to retrieve data. All data is retrieved via a secured connection (HTTPS) to ensure data integrity. Other than the request parameters, no data is sent to the Recras servers.

This plugin consists of the following "widgets". To use them, you first need to set your Recras name (see paragraph above).
* Availability calendar
* Book processes
* Contact forms
* Online booking of packages
* Packages
* Products
* Voucher sales
* Voucher info

Widgets can be added to your site in three ways. Using Gutenberg blocks (recommended), using the buttons in the "classic editor" (limited functionality), or by entering the shortcode manually (discouraged).

= Date picker for contact forms =
By default, date pickers in contact forms use the browser date picker. If you want to be able to style the date picker, we recommend to enable the date picker we have included with the plugin. You can enable this on the Recras → Settings page.

**Note**: this setting only applies to standalone contact forms, not to contact forms used in the seamless online booking integration or in book processes.

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

= Do you support Gutenberg? =
Yes, since version 2.2.0! Please make sure you use the latest version of the plugin and please report any bugs you encounter.

= Do you support Visual Composer, Elementor, Brizy, etc. ? =
We do not support page builders and have no plans to do so.

= Does the plugin support network installations? =
Yes it does. You can set different Recras names (all settings, for that matter) for each site.

= Can the plugin be installed as "must use plugin" ? =
No. "Must use" plugins don't appear in the update notifications nor show their update status on the plugins page (direct quote from the <a href="https://wordpress.org/support/article/must-use-plugins/">WordPress documentation</a>) which is reason enough for us not to support this.

== Screenshots ==

1. Example of a programme with the Twenty Fifteen theme
2. Example of package information, generated from Recras data
3. The Recras blocks in Gutenberg

== Changelog ==

= 6.0.2 =
* Add a few more German translations

= 6.0.1 =
* Fix warning when using PHP 8.2+

= 6.0.0 =
* Removed support for Composer Installers
* Updated "Tested up to" version to 6.4

= 5.5.2 =
* **No functional changes**
* Updated "Tested up to" version to 6.3
* **Deprecation notice**: we plan on removing support for Composer Installers in version 6.0 of the plugin. Please let us know if you use this to install our plugin!

= 5.5.1 =
* Fix "previous/next month" not being visible in a book process when using Elementor

= 5.5.0 =
* Make online booking of packages look better on narrow pages on large screens (i.e. desktop)
* Gutenberg contact form widget: properly show allowed packages
* Add option to fix BP date picker styling
* Plugin now requires PHP 7.2 or higher

= 5.4 (highlights) =
* Small improvements and bugfixes

= 5.3 (highlights) =
* Allow initial value in first widget of a book process, when the first widget is "package selection".
* Bugfixes

= 5.2 (highlights) =
* It is now possible to integrate multiple book processes on one page
* Small bugfixes

= Older versions =
See [the full changelog](https://github.com/Recras/recras-wordpress-plugin/blob/master/changelog.md) for older versions.

== Upgrade Notice ==
See changelog. We use semantic versioning for the plugin.

== Support ==
We would appreciate it if you use [our GitHub page](https://github.com/Recras/recras-wordpress-plugin/issues) for bug reports, pull requests and general questions. If you do not have a GitHub account, you can use the Support forum on wordpress.org.

We only support the plugin on the latest version of WordPress (which you should always use anyway!) and only on [actively supported PHP branches](https://www.php.net/supported-versions.php).

== Credits ==
* Icons from [Dashicons](https://github.com/WordPress/dashicons) by WordPress, released under the GPLv2 licence.
* Date picker is [Pikaday](https://github.com/Pikaday/Pikaday), released under the BSD/MIT licence.
* Country list is by [umpirsky](https://github.com/umpirsky/country-list), released under the MIT licence.
