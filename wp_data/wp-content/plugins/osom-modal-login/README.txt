=== Osom Modal Login ===
Contributors: osompress, esther_sola, nahuai, davidperalvarez
Donate link: https://osompress.com
Tags:  Login, modal, logout, login form, custom login, wordpress login, overlay, login popup
Requires at least: 5.0
Tested up to: 6.0
Stable tag: 1.1.2
Requires PHP: 5.6
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
 

== Description ==

Osom Modal Login lets you easily create a modal box displaying the WordPress login form. It automatically adds a menu item named "Login", which you can customize, at the end of the selected menu(s). Once you click on it, it will launch the login modal box.

Alternatively, you can also use the included shortcode to add the modal login box in any place of the web.

= Features =

With Osom Modal Login you can customize several parameters in the options page:
1. Set the title of the modal box.
2. Select the navigation menu where you want to add login/logout item. You can choose more than one or the option 'none' if you don't want to add it in any menu location.
3. Customize "Login" text.
4. Customize "Logout" text.
5. Set login and logout URL. 
6. Display/hide "Remember me" checkbox.
7. Display/hide "Did you forget your password" link.
8. Display Register link (optional).


You can also use the built-in shortcode to add the modal box any where in your website.

= Shortcode usage (optional) =

You just need to enclose your custom text in [osom-login] shortcode.
For example: [osom-login] Custom text [/osom-login]

= Dev Feature =
The plugin uses Vanilla JavaScript so you can use it even if you dequeue WordPress jQuery. It's always nice to keep the dependencies to the minimum.

### Follow Along:

* [Visit the OsomPress site](https://osompress.com/)
* [Follow on Twitter](https://twitter.com/osompress)

== Installation ==

This plugin can be installed directly from your site.

1. Log in and navigate to Plugins &rarr; Add New.
2. Type "Osom Modal Login" into the Search and hit Enter.
3. Locate the Osom Modal Login plugin in the list of search results and click **Install Now**.
4. Once installed, click the Activate link.
5. Now you have the new plugin available on WordPress.

It can also be installed manually.

1. Download the Osom Modal Login plugin from WordPress.org.
2. Unzip the package and move to your plugins directory.
3. Log into WordPress and navigate to the Plugins screen.
4. Locate Osom Modal Login in the list and click the *Activate* link.
5. Now you have the new plugin available on WordPress.


== Frequently Asked Questions ==

= Can I use Osom Modal Login with any theme? =

Yes, you can use Osom Modal Login with any theme. 

= Where can I modify Osom Modal Login settings? =

You can find the settings page on WordPress left sidebar under OsomPress > Osom Modal Login. 

= Can I use Osom Modal Login in other locations apart from the menus? =

Yes, you can add a login modal window anywhere on the website using the shortcode [osom-login] Custom text [/osom-login].

= Can I change the Login/Logout text? =

Yes, you can do it using a plugin like Loco Translate but we will add the option to do it from the settings page in an upcoming update. 

= Can I use Osom Modal Login on WordPress Multisite?  =

Yes, you can. Take into account that if you set the login or logout URL you will have to use an absolute URL, ie, https://yoursite.com/redirect-page. If you use a  relative ULR, such as /redirect-page/, it will point to the the main site URL (of the network).

= Is Osom Modal Login compatible with WordPress Multiligual plugin?  =

Yes, it is. 

= Will Osom Modal Login work on header/footers created with Elementor?  =

No at the moment. We will explore to add support on future updates.
 

== Screenshots == 
 
1. Dashboard plugin view
2. Front-end modal window 

== Changelog ==

= 1.1.2 =
* Remove Dashicons dependencie.
= 1.1.1 =
* Tested on WordPress 6.0.
* Fix new PHP notice (in PHP 8 or superior).
= 1.1 =
* Tested on WordPress 5.9.
* Fix PHP notices.
= 1.0.8 =
* Improve shortcode perfomance.
* Improve performance for multiple locations.
= 1.0.7 =
* Add option to display register link.
= 1.0.6 =
* Add option to select multiple menu location.
= 1.0.5 =
* Add redirection to the plugin settings page when activated.
* Tested on WordPress Multisite.
* Tested with WordPress Multilingual.
= 1.0.4 =
* Add labels for login/logut menu item
* Removal of the jQuery used and replaced with Vanilla JavaScript
* Add settings link in plugins page
= 1.0.3 =
* Fix warnings
= 1.0.2 =
* Translation improvements
= 1.0 =
* Initial release. 