(function($) {

    var mapQuery                = malinky_ajax_paging.mapQuery;
        mapMaxNumPages          = parseInt(malinky_ajax_paging.mapMaxNumPages),
        mapPostsWrapperClass    = malinky_ajax_paging.mapPostsWrapperClass;
        mapPostClass            = malinky_ajax_paging.mapPostClass;
        mapPaginationClass      = malinky_ajax_paging.mapPaginationClass;
        mapNextPage             = parseInt(malinky_ajax_paging.mapNextPage),
        mapNextPageUrl          = malinky_ajax_paging.mapNextPageUrl,
        mapLoadingTimer         = '';

    var MalinkyAjaxPaging = {

        /**
         * Initialize.
         * Remove the existing and add new pagination.
         */
        init : function() {
            $(mapPaginationClass).empty();
            $(mapPaginationClass).append('<a href="' + mapNextPageUrl + '" class="malinky-ajax-paging-load-more-button button full-width">Load More</a>');
            $(mapPaginationClass).before('<div class="malinky-ajax-paging-loading"></div>');
            $('.malinky-ajax-paging-loading').before('<div class="map-loading-placeholder-' + mapNextPage + '"></div>');
        },


        /**
         * Add loading text to button and loader.gif
         */
        mapLoading : function() {
            $('.malinky-ajax-paging-load-more-button').text('Loading...');
            $('.malinky-ajax-paging-loading').show();
        },


        /**
         * Remove loading text to button and loader.gif
         */
        mapLoaded: function () {
            $('.malinky-ajax-paging-load-more-button').text('Load More');
            $('.malinky-ajax-paging-loading').hide();
        },

        /**
         * Load and append posts.
         */
        mapPosts: function () {
            /**
             * Load page content with jQuery .load()
             */
            //$('.map-loading-placeholder-' + mapNextPage).load(mapNextPageUrl + ' ' + mapPostClass , function() {
            //    MalinkyAjaxPaging.mapNextPageSetup();
            //});

            $.ajax({
                    type:       'GET',
                    url:        mapNextPageUrl,
                    //data:       data,
                    dataType:   'html',
                    success:    function(response) {
                                    /**
                                     * Debug result, also set in malinky_ajax_paging_submit() and malinky_ajax_paging_wp_query().
                                     * console.log(result);
                                     */
                                    var mapResponse = $.parseHTML(response); 
                                    $(mapPostsWrapperClass + ' ' + mapPostClass + ':last-child').after($(mapResponse).find(mapPostsWrapperClass + ' ' + mapPostClass));
                                    MalinkyAjaxPaging.mapNextPageSetup();
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
        },

        /**
         * Prepare and increment variables for next page of posts.
         * Called after posts have been appended.
         */
        mapNextPageSetup: function() {
            /**
             * Remove loading div and clear timers
             */
            MalinkyAjaxPaging.mapLoaded();
            clearTimeout(mapLoadingTimer);

            /**
             * Increment page number.
             */
            mapNextPage++;
                
            /**
             * Add new incremental placeholder as load() replaces contents not append.
             */
            $('.malinky-ajax-paging-loading').before('<div class="map-loading-placeholder-' + mapNextPage + '"></div>');

            /**
             * Check the new next page number is not greater than the max pages.
             */
            if (mapNextPage > mapMaxNumPages) {
                $(mapPaginationClass).remove();
                return false;
            }

            /**
             * Create new page url for button link.
             */
            mapNextPageUrl = mapNextPageUrl.replace(/\/page\/[0-9]?/, '/page/'+ mapNextPage);

        }

    } //MalinkyAjaxPaging
    
    MalinkyAjaxPaging.init();

    /**
     * Use .on as the pagination is added after page load and we need to use delegated event.
     */
    $(mapPaginationClass).on( 'click', '.malinky-ajax-paging-load-more-button', function(event) {

        /**
         * Delay loading text and div.
         */
        mapLoadingTimer = setTimeout(MalinkyAjaxPaging.mapLoading, 750);

        /**
         * Debug timer. Remove mapPosts call and use setTimeout instead.
         * setTimeout(MalinkyAjaxPaging.mapPosts, 3000);
         */
        
        /**
         * Load more posts.
         */
        MalinkyAjaxPaging.mapPosts();

        event.preventDefault();

    });    

})(jQuery);