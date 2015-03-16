var MalinkyAjaxPaging = (function($) {

    var mapQuery                = malinky_ajax_paging.mapQuery,
        mapMaxNumPages          = parseInt(malinky_ajax_paging.mapMaxNumPages),
        mapPostsWrapperClass    = malinky_ajax_paging.mapPostsWrapperClass,
        mapPostClass            = malinky_ajax_paging.mapPostClass,
        mapPaginationClass      = malinky_ajax_paging.mapPaginationClass,
        mapNextPage             = parseInt(malinky_ajax_paging.mapNextPage),
        mapNextPageUrl          = malinky_ajax_paging.mapNextPageUrl,
        mapLoadingTimer         = '';


    /**
     * Initialize.
     * Remove the existing pagination and add new.
     */
    var init = function() {
        $(mapPaginationClass).empty();
        $(mapPaginationClass).append('<a href="' + mapNextPageUrl + '" class="malinky-ajax-paging-button button full-width">Load More</a>');
        $(mapPaginationClass).before('<div class="malinky-ajax-paging-loading"></div>');
    };


    /**
     * While new posts are loaded.
     * Add loading text to button.
     * Show loader.gif.
     * This function is called using a setTimeout of 750 in the click event handler.
     */
    var mapLoading = function() {
        $('.malinky-ajax-paging-button').text('Loading...');
        $('.malinky-ajax-paging-loading').show();
    };


    /**
     * After new posts have been loaded.
     * Add loading text to button.
     * Hide loader.gif.
     */
    var mapLoaded = function () {
        $('.malinky-ajax-paging-button').text('Load More');
        $('.malinky-ajax-paging-loading').hide();
    };


    /**
     * Load new posts and append to exists content.
     */
    var mapLoadPosts = function () {
        $.ajax({
                type:       'GET',
                url:        mapNextPageUrl,
                dataType:   'html',
                success:    function(response) {
                                //Parse HTML as stops scripts being run in fill html response.
                                var mapResponse = $.parseHTML(response); 
                                $(mapPostsWrapperClass + ' ' + mapPostClass + ':last-child').after($(mapResponse).find(mapPostsWrapperClass + ' ' + mapPostClass));
                                mapNextPageSetup();
                            }
        });

        /**
         * MAL$.ajax
         * Else use ajax get. This uses a new WP_Query and template part.
         *
        var data = {
            action:        'malinky-ajax-paging-submit',
            mapNextPage:   mapNextPage,
            mapQuery:      mapQuery
        };
        /**
         * Load the content of the next page.
         * Find the .archive-content divs only found in content.php.
         * Add after the last article of .mapPostsWrapperClass.
         * .after() is used as it doesn't add whitespace where using .append() breaks the layout due to
         * the use of display: inline-block.
         *
        $.ajax({
                type:       'GET',
                url:        malinky_ajax_paging.ajaxurl,
                data:       data,
                success:    function(response) {
                                var result = $.parseJSON(response);
                                /**
                                 * Debug result, also set in malinky_ajax_paging_submit() and malinky_ajax_paging_wp_query().
                                 * console.log(result);
                                 *
                                $(mapPostsWrapperClass + ' ' + mapPostClass + ':last-child').after(result.malinky_ajax_paging_posts);
                                mapNextPageSetup();
                            }
        });*/
    };


    /**
     * Prepare variables for the next set of posts.
     * This is called in the success callback of of $a.ajax in mapLoadPosts().
     */
    var mapNextPageSetup = function() {
        //Remove loading div and clear timers
        mapLoaded();
        clearTimeout(mapLoadingTimer);
        //Increment page number.
        mapNextPage++;
        //Check we're not on the last page and all posts have been loaded.
        if (mapNextPage > mapMaxNumPages) {
            $(mapPaginationClass).remove();
            return false;
        }
        //Update next page url.
        mapNextPageUrl = mapNextPageUrl.replace(/\/page\/[0-9]?/, '/page/'+ mapNextPage);
    };


    /**
     * Attach a click event handler to the original pagination class.
     * The original pagination class is present on the initial page load.
     * When the new .malinky-ajax-paging-button is clicked it bubbles up one level to mapPaginationClass.
     * This is known as a delegated event.
     */
    $(mapPaginationClass).on( 'click', '.malinky-ajax-paging-button', function(event) {
        event.preventDefault();
        //Delay loading text and div.
        mapLoadingTimer = setTimeout(mapLoading, 750);
        //Load more posts.
        mapLoadPosts();
        /**
         * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
         * setTimeout(MalinkyAjaxPaging.mapPosts, 3000);
         */
    });


    //Launch
    init();

})(jQuery);