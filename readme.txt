=== Common Wish and Bridal Lists ===
Contributors: briar
Donate link: http://briar.fun/donate/
Tags: wishlist, wish list, bridallist, bridal list, woocommerce, add to wish list, add to bridal list, wedding list
Requires at least: 4.7
Tested up to: 5.4.2
Stable tag: 1.3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A comprehensive, modern and flexible Wish and Bridal lists for WooCommerce.

== Description ==

Plugin allows customer add product in wish or bridal list for WooCommerce. User can buy or share list.

= Features =
* Add and remove product.
* Changing quantity added product.
* Setting location button.
* Setting icons FontAwesome for link .
* Shortcode for list page.
* Hook and function for developer.

Before using create page on which place shortcode `[wishlist]` (optionally repeat with `[bridallist]`). Then set this page in Pages section setting (Wocommerce -> Products -> Wish&Bridal List). You can choose which location will be displayed on the link "Add to list" as well as enable or disable functionality Bridal List. Also optionally select the icon that will appear before links and to distinguish their condition.

If you wish to place a link on a page in the list with number added products (like cart link), use shortcodes [wishlist_count] or [bridallist_count].

If you selected link position None use function `wb_wish_link` or `wb_bridal_link` for display link. For some other function check /includes/wb-functions.php.

= User Guide =
1. Bridal List
1.1 Bridal list is only for logged in users. Every registered user can create one Bridal list.
1.2. Every registered user can customize his own Bridal List and add personal data.
1.3. Every product can be added to/removed from Bridal List on a product card page. Number of pieces of the desired product can be configured on the page of Bridal List.
1.4. Owner of Bridal List can share his Bridal List.
1.5. Site visitors can buy one or more products from Bridal List. The Bridal List displays items that have not been purchased.
2. Configuring and administration of the store by Administrator.
2.1. Administrator customizes the appearance of the store.


== Installation ==

1. Upload 'wish-bridal-list' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Frequently Asked Questions ==

= Why Add to Bridal List link is inactive? =
Bridal list is only for logged in users.

= How place shortcode in themes file? =
Shortcode can be used anywhere in the theme templates via do_shortcode function. For example, `<?php do_shortcode('[wishlist_count]')?>`.


== Screenshots ==

1. Add to list links on single product page.
2. Wish List page.
3. Bridal List page.
4. Plugin settings.

== Changelog ==

= 1.3.1 - 2020-07-22 =
* Changed - Wordpress 5.4.2 and Woocommerce 4.3.0 compatibility.

= 1.3.0 - 2018-10-25 =
* Fixed: Some strings could not be translated.
* Changed: Wordpress 5.0 and Woocommerce 3.5 compatibility.

= 1.1.0 - 2017-22-08 =
* Changed: Woocommerce 3.1 and Wordpress 4.8 compability.

= 1.0.5 - 2017-17-01 =
* Changed: Markup product listing.
* Changed: Improved UI.
* Fixed: Start Woocommerce session even empty cart.

== Upgrade Notice ==
The current version of Common Wish and Bridal Lists requires WordPress 4.7 or higher. If you use older version of WordPress, you need to upgrade WordPress first.