=== Ajax Pagination and Infinite Scroll ===
Contributors: malinkymedia
Tags: admin, AJAX, ajax pagination, back, custom post types, forward, history, infinite, infinite scroll, infinite scrolling, load more, load more button, navigation, next, options, page, pages, pagination, paging, post types, post, posts, previous, scroll, scroll to top
Requires at least: 3.6.0
Tested up to: 4.7.2
Stable tag: 2.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Choose infinite scroll, load more button or pagination. Load paged content with Ajax on your posts, pages, search, custom post types and WooCommerce.

== Description ==

Load paged content with Ajax throughout your Wordpress site. The plugin works on posts, pages, search, custom post types and WooCommerce. Paginate MULTIPLE sets of posts in the same template.

Take a look at the <a href="http://www.wordpress-ajax-pagination.com" target="_blank">demo site</a>.

We're looking for websites that use the plugin to feature on a showcase page. If you’re interested please email a link to your website to <a href="mailto:hello@wordpress-ajax-pagination.com?subject=Website Submission">hello@wordpress-ajax-pagination.com</a>.

There are 3 pagination types to choose from.

* Infinite Scroll - Automatically load new posts when the user reaches the bottom of the screen.
* Load More Button - Click to load new posts.
* Pagination - Normal pagination but load the next page with Ajax.

= Set Up =

* For additional help watch this <a href="http://www.wordpress-ajax-pagination.com/set-up" target="_blank">video</a>.
* Once the plugin is installed navigate to Settings -> Ajax Pagination Settings.
* Select an applicable theme default. If your theme isn't listed then add the correct selectors.
* There are 4 required selectors which can be found by using your browser developer tools.
* 'Posts Selector' The selector that wraps all of the posts/products.
* 'Post Selector' The selector of an individual post/product.
* 'Navigation Selector' The selector of the post/product navigation.
* 'Next Selector' The selector of the navigation next link.
* Choose a pagination type.
* Add an optional callback.
* Click 'Save Changes'.

= Multiple Settings =

If for example your site is running WooCommerce and also a Blog then the two templates will probably use different selectors and require a different set up from the admin settings.

* Click 'Add New' at the top of the screen.
* Repeat the set up steps above and click 'Save Changes'.
* At the top of the screen you can navigate between your saved settings.

= Multiple Sets of Posts =

It's possible to query and display multiple sets of posts in the same template and independently paginate them. This requires a specific set up within the template file which has been outlined <a href="http://www.wordpress-ajax-pagination.com/multiple-posts-set-up" target="_blank">here</a>.

= Delete Setting =

* At the top of the screen select the pagination setting you wish to delete.
* Scroll to the bottom of the screen and click 'Delete'.

= Styling Load More Button =

* The load more button is wrapped in a div with the css class .malinky-load-more and the button has the css class .malinky-load-more__button.
* Style these in your themes style.css file.

= Initialization (Setup) =

The plugin is initialized when the page first loads and the pagination is then updated to use Ajax. If you need to reinitialize the plugin at anytime call the folowing function in your javascript.

    MalinkyAjaxPaging.setUp();

This is useful if you run any other javascript / ajax on the page, for example filtering on an ecommerce site.

= Callback =

* Add your own Javascript code in the settings which runs after each new set of posts are loaded.
* Callback receives two parameters: loadedPosts (An array of the new posts) and url (The url that was loaded).

= Custom Events =

There are 3 custom events that are triggered. Bind to these events to perform your own actions.

* malinkyPaginationInitialized. Triggered after initialization.

    `document.addEventListener('malinkyPaginationInitialized', function(e) {
      console.log('PaginationInitialized');
    });`

* malinkyLoadPostsStart. Triggered before new posts are loaded.

    `document.addEventListener('malinkyLoadPostsStart', function(e) {
      console.log('LoadPostsStart');
    });`

* malinkyLoadPostsComplete. Triggered after new posts are loaded.

    `document.addEventListener('malinkyLoadPostsComplete', function(e) {
      console.log('LoadPostsComplete');
    });`

* Events only function when using a single set of pagination on a page.

= Isotope / Masonry =

If your using an isotope / masonry layout then you'll need to add a callback in the settings to layout the new posts when they are added. This should be in the following format.

    $('.grid').isotope('reloadItems').isotope();

Change the reference to the class name .grid to your own container element.

= Additional =

* If using infinite scroll there is an option to amend the buffer in pixels before the next set of posts are loaded.
* If using load more button there is an option to amend the button text.
* You can choose your own preloader.gif.
* When using pagination the browser history is maintained allowing your visitors to use the browser back and forward buttons as expected.

= Conditional Loading =

By default the plugin loads on every page load. You can stop loading the Javascript and CSS by setting the following constants to false in your wp-config.php file.

    define('MALINKY_LOAD_JS', false);
    define('MALINKY_LOAD_CSS', false);

Or by adding these two lines to your functions.php

    add_filter( 'malinky_load_js', '__return_false' );
    add_filter( 'malinky_load_css', '__return_false' );

Once disabled you can load the Javascript and CSS in specific templates by adding the following before the wp_head().

    global $malinky_ajax_pagination;
    $malinky_ajax_pagination->malinky_ajax_pagination_styles();
    $malinky_ajax_pagination->malinky_ajax_pagination_scripts();

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

= 2.0.1 =

* Fix custom event IE bug.

= 2.0.0 =
* Added a javascript function that allows the plugin to be initialized at anytime. This is useful if you need to run additional javascript following the initial page load.
* Added 3 custom events that fire during stages of the pagination.
* Fix issue where pagination clicks didn't trigger the correct target.

= 1.3.0 =
* Removed the localization of max_num_pages and rewritten the last page selection in main.js.
* Fix issue where pagination couldn't always find the last page.
* Fix issue in callback for finding the current url.
* Fix error before any settings have been saved.

= 1.3.0 =
* Removed the localization of max_num_pages and rewritten the last page selection in main.js.
* Fix issue where pagination couldn't always find the last page.
* Fix issue in callback for finding the current url.
* Fix error before any settings have been saved.

= 1.2.2 =
* Fix issue where pagination couldn't always find the last page.
* Fix issue in callback for finding the current url.

= 1.2.1 =
* Fix issue where double clicked load more button could fire twice and load the same posts.

= 1.2.0 =
* Added callback to run after each set of new posts are loaded.
* Removed conditional load so plugin now loads on every post type. See conditional loading notes to enqueue only when required.

= 1.1.1 =
* Allow posts pagination on single templates. An example would be a sidebar showing category posts with a load more button. **NOTE** This is not to paginate through single posts.

= 1.1.0 =
* Independently paginate through multiple sets of posts.
* Fix duplicate page numbers being loaded when pagination option is used and the page contains more than one set of the same navigation.
* Fix issue where infinite scroll could fire twice and load the same posts.
* Allow the pagaintion of search page templates.
* Add Twenty Sixteen theme to list of defaults.
* Updates to admin button styles.

= 1.0.0 =
* Ajax Pagination and Infinite Scroll