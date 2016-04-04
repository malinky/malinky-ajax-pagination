=== Ajax Pagination and Infinite Scroll ===
Contributors: malinkymedia
Tags: admin, AJAX, ajax pagination, back, custom post types, forward, history, infinite, infinite scroll, infinite scrolling, load more, load more button, navigation, next, options, page, pages, pagination, paging, post types, post, posts, previous, scroll, scroll to top
Requires at least: 3.6.0
Tested up to: 4.3
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Choose from infinite scroll, load more button and pagination to load paged content with Ajax on your posts, pages, custom post types and WooCommerce.

== Description ==

Load paged content with Ajax throughout your Wordpress site. This plugin works where you'd usually display paged content including on posts, pages, custom post types and WooCommerce.

There are 3 paging types to choose from; infinite scroll that automatically loads new posts as the user reaches the bottom of the screen, a load more button that when clicked loads new posts and pagination that displays normal pagination but loads the next page with Ajax. When using pagination the browser back and forward button still operate as expected.

All 3 options deliver a great user experience.

Multiple pagination settings can be created for different post types and templates throughout your site. For example, if you were running a WooCommerce site which contained a blog you could use different pagination types for each.

For a quicker setup options for popular themes are included. If your theme isn't listed you can simply overwrite the settings with the correct selectors.

= Set Up =

* Once the plugin is installed navigate to Settings -> Ajax Pagination Settings.
* Select an applicable theme default. If your theme isn't listed then add the correct selectors.
* There are 4 selectors required which can be found by using your browsers developer tools to locate the correct divs.
* 'Posts Selector' The selector that wraps all of the posts/products.
* 'Post Selector' The selector of an individual post/product.
* 'Navigation Selector' The selector of the post/product navigation.
* 'Next Selector' The selector of the navigation next link.
* Choose a pagination type.
* Click 'Save Changes'.
* Your Ajax pagination will now be working.

= Add Mulitple Pagination Settings =

* Once you have saved one set of pagination settings you can click 'Add New' at the top of the screen.
* Repeat the previous steps by choosing the correct selectors and click 'Save Changes'.
* At the top of the screen you will now see your saved settings and can navigate between them if you wish to amend anything.

= Delete Pagination Setting =

* At the top of the screen select the pagination setting you wish to delete.
* Scroll to the bottom of the screen and click 'Delete'.

= Styling Load More Button =

* The load more button is wrapped in a div with the class name malinky-load-more and the actual button has the class name malinky-load-more__button.
* Style these in your themes style.css file.

= Additional =

* If using infinite scroll there is an option to amend the buffer in pixels before the next set of posts are loaded.
* If using load more button there is an option to amend the button text.
* You can choose your own preloader.gif.

== Installation ==

= Using the WordPress Dashboard =

1. Click 'Add New' in the plugins dashboard.
2. Search for 'Ajax Pagination and Infinite Scroll'.
3. Click 'Install Now'.
4. Activate the plugin in the plugins dashboard.

= Upload in the WordPress Dashboard =

1. Click 'Upload Plugin' in the plugins dashboard.
2. Choose 'malinky-ajax-pagination.zip' from your computer.
3. Click 'Install Now'.
4. Activate the plugin in the plugins dashboard.

= Upload With FTP =

1. Upload the 'malinky-ajax-pagination' folder to the '/wp-content/plugins/' directory
2. Activate the plugin in the plugins dashboard.

== Screenshots ==

1. Admin screen.
2. Click 'Add New' for multiple pagination settings.
3. Navigate between multiple pagination settings.
4. Delete pagination setting.

== Changelog ==

= 1.0.0 =
Ajax Pagination and Infinite Scroll