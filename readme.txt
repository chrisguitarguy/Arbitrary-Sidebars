=== Arbitrary Sidebars ===
Contributors: chrisguitarguy
Donate link: http://www.pwsausa.org/give.htm
Tags: sidebars, sidebar, widget
Requires at least: 3.4
Tested up to: 3.5
Stable tag: 1.1
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add widget areas through an admin page.

== Description ==

Arbitrary Sidebars allows you to add widget areas via an page in the WordPress admin area. What you do with those sidebars on the front end is up to you and your theme.

**This plugin requires PHP 5.3+**. Please check your server configuration before installing.

Some use case ideas:

* The end user wants to associate sidebars with individual posts and pages. This plugin ould provide half that functionality: easily creating widget areas.
* You need reusable content blocks and want to take advantage of the widget system.

== Installation ==

Install via the WP Admin installer.  OR...

1. Click the big button and grab the plugin zip file.
2. Unzip it
3. Upload the `arbitrary-sidebars` folder to your `wp-content/plugins` directory
4. Activate the plugin via the admin area.

== Frequently Asked Questions ==

= How can I change the args paged to register_sidebar? =

First off, the sidebar name and ID are defined in the admin screen.

There are a few filters you can hook into and use to modify other args.

Hook into `arbitrary_sidebars_args` to modify the the sidebar arguments for the entire plugin.

Hook into `arbitrary_sidebars_single_args` to modify the arguments for a single sidebar based on its ID.

== Screenshots ==

1. View of the admin list table, where you see all the sidebars current registered with this plugin.
2. The form to add a new sidebar.

== Changelog ==

= 1.0 =
* First release
* Allows user to add widget areas via an admin screen

== Upgrade Notice ==

= 1.0 =
* Widgets are fun, try it!
