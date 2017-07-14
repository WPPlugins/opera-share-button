=== Opera Share Button ===
Contributors: klay
Donate link: http://klays.ru/donate
Tags: Opera, share, social, button, post
Requires at least: 3.0
Tested up to: 3.4.1
Stable tag: 0.1.5.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

The simplest way for embedding social sharing buttons for the Opera Network!

== Description ==

Includes support for the Share to My Opera Community page and Follow My Opera profile page buttons. People will be sharing your posts on My Opera Community before you know it and giving you more traffic and readers on your site.

One click setup and start providing link to your My Opera profile page and sendings for posts. The buttons can be embedded via a shortcode, or automatically before the content, after the content or in both positions.

= Features =

* Actions: Ability to add follow button in the most easiest and flexible way.
* Actions: Ability to add My Opera Share button in the most easiest and flexible way.
* Actions: Ability to choose as will be opened My Opera windows.
* Display: This plugin allows you to select the position for the button: before, after, before and after or using shortcode.
* Display: Ability to use Cascading Style Sheets from the plugin directory.

= Translation =

* Russian (ru_RU)

If you create your own language pack or update an existing one, you can send [text in PO and MO files](http://codex.wordpress.org/Translating_WordPress) for [Opera Share Button on Git Repository](https://github.com/sergeyklay/OSB) and we'll add it to the plugin.

= Technical support =

Dear users, if you have any questions or propositions regarding our plugins (current options, new options, current issues) please feel free [to send issue in github](https://github.com/sergeyklay/OSB/issues). Please note that we accept requests in English or Russian. All messages on another languages wouldn't be accepted.

Development version: [https://github.com/sergeyklay/OSB/tree/next](https://github.com/sergeyklay/OSB/tree/next)

== Installation ==

The plugin requires WordPress 3.0 and is installed like any other plugin.

1. Upload the plugin to the `/wp-contents/plugins/` folder.
2. Activate the plugin from the 'Plugins' menu in WordPress.
3. Configure the plugin by following the instructions on the `Opera Buttons` settings page.

== Frequently Asked Questions ==

= I cannot see Opera Share Button icons in the post after plugin installation =

In WordPress admin panel go to "Plugins", find "Opera Share Button" and press "Activate".

= How I can deactivate the plugin =

In WordPress admin panel go to "Plugins", find "Opera Share Button" and press "Deactivate".

= How to adjust Opera Share Button icons position on the page =

In WordPress admin panel go to "Settings", find "Opera Buttons" settings page and choose one from listed positions: Before, After, Before and After or Shortcode. Then press "Save Changes" button.

= Can I use the shortcode? =

Yes. Configure Opera Share Button plugin as appropriate on `options-general.php?page=osb` page and put [opera_buttons] shortcode in your posts.

= After installation and setting the plugin at the settings page it is still not working =

You need to press "Save Changes" button to update all the changes. Make sure that you got "All changes were saved successfully" message after saving action.

= Will the plugin work with versions prior to 3.0? =

No. Plugin not work with Wordpress versions prior to 3.0.

= Can I use custom CSS file? =

Yes! You can use CSS in the plugin directory: `osb/css/opera-buttons.css`.

== Screenshots ==

1. Opera Share Button plugin admin page
2. Displaying Opera Buttons into your post

== Changelog ==
= 0.1.5.1 =
* Fixed bug with mechanism of localization

= 0.1.5 =
* Added target atribte for buttons and appropriate setting option
* Modified plugin info and description
* Localized new strings into Russian ( [klay](http://profiles.wordpress.org/klay/) )
* Added new strings for localize

= 0.1.4 =
* Updated mechanism for loading CSS
* Fixed a bug that causes adding the same link to all posts
* Removed JS because it is not needed

= 0.1.3 =
* Added ability to use CSS form the plugin directory
* Fixed localization
* Changed the mechanism of busting positions of buttons
* A more laconic code

= 0.1.2 =
* Added POT file for localization into any languages
* Addig Russian Localization ( [klay](http://wordpress.org/support/profile/klay) )
* Amended and supplemented readme.txt for better displaying information about plugin in Wordpress.org
* The plugin now properly handles updates. This means you won't have to reactivate the plugin after each update
* Minor bugfixies

= 0.1.1 =
* Minor changes (typos, banner and etc.)

= 0.1 =
* Initial release

== Upgrade Notice ==

= 0.1.5.1 =
Fixed bug with mechanism of localization. Upgrade immediately!

= 0.1.5 =
Now, buttons use target atribute (_self, _blank)

= 0.1.4 =
This version fixes a bug that causes adding the same link to all posts. Upgrade immediately! Also updated mechanism for loading CSS and removed JS because it is not needed.

= 0.1.3 =
Added ability to use CSS form the plugin directory. Fixed Russian Localization. Changed the mechanism of busting positions of buttons. Minor buxfixes.

= 0.1.2 =
Usability at the settings page of the plugin was improved. Internationalization support. Russian Localization. Upgrading mechanism. Minor buxfixes.
