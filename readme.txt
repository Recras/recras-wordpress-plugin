=== Recras WordPress plugin ===
Contributors: zanderz
Tags: recras, recreation, reservation, booking, voucher
Tested up to: 6.1
Stable tag: 5.1.5
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

= Date/Time picker =
By default, date and time pickers in contact forms use whatever the browser has available. Internet Explorer (all versions) does not have native date/time pickers and only shows a text field. If your website has a lot of visitors from IE, we recommend to enable the date picker we have included with the plugin. You can enable this on the Recras → Settings page. For time inputs, a proper fallback is included.

**Note**: this setting only applies to standalone contact forms, not to contact forms used in the seamless online booking integration.

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

= Do you support Visual Composer, Brizy, etc. ? =
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

= 5.1.5 =
* Book process: fix date picker button styling

= 5.1.4 =
* Voucher sales no longer shows error messages inline, but after the form

= 5.1.3 =
* Improve book process styling

= 5.1.2 =
* Seamless online booking integration: Fix entering DD-MM-YYYY dates by hand

= 5.1.1 =
* Fix old online booking in an iframe

= 5.1.0 =
* "Theme for online booking" has been renamed to "Theme for Recras integrations" and now also applies to contact forms
* Fix error with online booking iframe
* Fix green buttons in blue/red themes

= 5.0 (highlights) =
**Major release** Please read the following changes carefully:

* Seamless online booking integration:
  * Fix GA4 events
  * Support for Google Analytics v2 has been dropped
  * Support for Internet Explorer and old Edge (12-15) has been dropped
  * number of people on a package line was used as minimum quantity. This has been fixed
* Plugin now requires PHP 7.1 or higher
* Fix error when using "Image URL" of a product/package without image
* Add "Products" section to documentation/shortcode pages
* Various small fixes

= 4.8 (highlights) =
* Book processes: update styling for empty inputs and capacity info in "product with time" blocks
* Fix loading of book processes on some sites
* Seamless online booking integration: add support for GA4

= 4.7.10 =
* Fetching thank-you pages is a lot faster now
* Show "Loading data" message while fetching thank-you pages

= 4.7.9 =
* The changes made in 4.7.7 didn't work properly. Dropdowns now show all pages/posts.

= 4.7.8 =
* Make default settings work properly

= 4.7.7 =
* Dropdowns for "Thank-you page" showed 100 pages/posts. This has been increased to 250 of each.

= 4.7.6 =
* Not released due to an error

= 4.7.5 =
* Fix page crashing when trying to show the duration of a package where the last line has no end time

= 4.7.4 =
* Seamless online booking integration: change value of "BuyInProgress" events from package/template ID (bookings/vouchers, respectively) to total price

= 4.7.3 =
* Fix wrong page width when using an online booking theme

= 4.7.2 =
* Fix page crashing when trying to show the programme of a multi-day package where the last line has no end time
* Small styling update for book process calendar

= 4.7.1 =
* Fix page crashing when trying to show the duration of a package that does not exist

= 4.7.0 =
* Fix "Class not found" error when using Composer in a theme
* Update themes for use in book processes and add two new themes


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
