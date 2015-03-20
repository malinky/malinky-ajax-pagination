var MalinkyAjaxPaging = (function($) {

    //If no pagination exists return false, not required.
    if ( $(malinky_ajax_paging_options.pagination_wrapper).length == 0 && $(malinky_ajax_paging_options.woo_pagination_wrapper).length == 0 ) {
        return false;
    }

    /**
     * Variables, some from wp_localize_script().
     * Note the mapIsWooCommerce check to determine what property to use when setting the following.
     * mapPaginationClass, mapPostsWrapperClass, mapPostClass, mapNextPageSelector.
     * As mapNextPageUrl is optional if not set use the WP_Query.
     */
    var mapIsWooCommerce                    = malinky_ajax_paging_options.is_woocommerce,
        mapAjaxLoader                       = malinky_ajax_paging_options.ajax_loader,
        mapCssLoadMore                      = malinky_ajax_paging_options.malinky_load_more,
        mapCssLoadMoreButton                = malinky_ajax_paging_options.malinky_load_more_button,
        mapInfiniteScrollBuffer             = parseInt(malinky_ajax_paging_options.infinite_scroll_buffer),
        mapLoadingTimer                     = '',
        mapLoadingMorePostsText             = malinky_ajax_paging_options.loading_more_posts_text,
        mapLoadMoreButtonText               = malinky_ajax_paging_options.load_more_button_text,
        mapPaginationClass                  = !mapIsWooCommerce ? malinky_ajax_paging_options.pagination_wrapper : malinky_ajax_paging_options.woo_pagination_wrapper,
        mapPaginationClassPixelsToDocBottom = $(document).height() - $(mapPaginationClass).offset().top,
        mapPagingType                       = malinky_ajax_paging_options.paging_type,
        mapPostsWrapperClass                = !mapIsWooCommerce ? malinky_ajax_paging_options.posts_wrapper : malinky_ajax_paging_options.woo_posts_wrapper,
        mapPostClass                        = !mapIsWooCommerce ? malinky_ajax_paging_options.post_wrapper : malinky_ajax_paging_options.woo_post_wrapper,
        mapMaxNumPages                      = parseInt(malinky_ajax_paging_options.max_num_pages),
        mapNextPageNumber                   = parseInt(malinky_ajax_paging_options.next_page_number),
        mapNextPageSelector                 = !mapIsWooCommerce ? malinky_ajax_paging_options.next_page_selector : malinky_ajax_paging_options.woo_next_page_selector,
        mapNextPageUrl                      = $(malinky_ajax_paging_options.next_page_selector).attr('href') || malinky_ajax_paging_options.next_page_url;


    /**
     * Initialize.
     */
    var init = function() {

        if (mapPagingType == 'infinite-scroll') {

            //Add loader.gif div.
            mapAddLoader();
            //Remove existing pagination.
            $(mapPaginationClass).remove();
            /**
             * Attach scroll event listener to the window.
             * See mapInfiniteScroll function.
             */
            window.addEventListener('scroll', mapInfiniteScroll);

        } else if (mapPagingType == 'load-more') {

            //Add new pagination button after last mapPaginationClass.
            //Use last() as some themes don't wrap navigation and this only adds loader.gif div once.
            $(mapPaginationClass).last().after('<div class="' + mapCssLoadMore + '"><a href="' + mapNextPageUrl + '" id="malinky-ajax-paging-button" class="' + mapCssLoadMoreButton + '">' + mapLoadMoreButtonText + '</a></div>');
            //Add loader.gif div.
            mapAddLoader();
            //Remove the existing pagination.
            //Search for mapPaginationClass but don't remove if child contains #malinky-ajax-paging-button.
            //This would be the new navigation if the user has set a css class the same as the original mapPaginationClass.
            $(mapPaginationClass + ':not(:has(>a#malinky-ajax-paging-button))').remove();
            
            /**
             * Attach a click event handler to the new pagination button.
             * No longer use delegate event as this click event is added after the new pagination button is added to the dom.
             */
            $('#malinky-ajax-paging-button').click(function(event) {
                event.preventDefault();
                //Delay loading text and div.
                mapLoadingTimer = setTimeout(mapLoading, 750);
                //Load more posts.
                mapLoadPosts();
                /**
                 * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
                 * setTimeout(mapLoadPosts, 3000);
                 */
            }); 

        } else if (mapPagingType == 'pagination') {

            //Add loader.gif div.
            mapAddLoader();
            /**
             * Attach a click event handler to the pagination links.
             * The pagination class is reloaded after a page change to update the page numbers therefore a delegated event is used.
             * This is attached to the document as it's the only item we can be sure to be there on first page load.
             * @link http://api.jquery.com/on/
             */
            $(document).on('click', mapPaginationClass, function(event) {
                event.preventDefault();
                //Delay loading text and div.
                mapLoadingTimer = setTimeout(mapLoading, 750);
                //Get the url of the clicked link.
                mapNextPageUrl = event.target.href;
                //Load more posts.
                mapLoadPosts();
                /**
                 * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
                 * setTimeout(mapLoadPosts, 3000);
                 */
            });
            /**
             * Attach popstate event listener which is triggered on the click of the browser back button.
             * The url generated by the browser back/forward button is saved in mapNextPageUrl and mapLoadPosts called.
             */
            window.addEventListener('popstate', function(event) {
                mapNextPageUrl = document.URL;
                mapLoadPosts();
            });

        }
     
    };


    /**
     * Load new posts and append to or replace existing posts depending on paging_type.
     */
    var mapLoadPosts = function () {
        $.ajax({
                type:       'GET',
                url:        mapNextPageUrl,
                dataType:   'html',
                success:    function(response) {

                                //Parse HTML first.
                                var mapResponse = $.parseHTML(response);

                                //Find the posts from the full html response using mapPostClass.
                                var $mapLoadedPosts = $(mapResponse).find(mapPostClass);

                                //jQuery object of the last currently displayed post.
                                var $mapInsertPoint = $(mapPostsWrapperClass + ' ' + mapPostClass).last();

                                //Save the existing posts if they are to be removed after insertion mapPagingType == 'pagination'.
                                var $mapExistingPosts = $(mapPostClass);

                                //Insert the posts after the last currently displayed post.
                                $mapInsertPoint.after($mapLoadedPosts);
                                
                                if (mapPagingType == 'infinite-scroll' || mapPagingType == 'load-more') {
                                    //Increment page number.
                                    mapNextPageNumber++;
                                    //Check we're not on the last page and all posts have been loaded.
                                    if (mapNextPageNumber > mapMaxNumPages) {
                                        //mapPagingType == 'load-more'.
                                        $('#malinky-ajax-paging-button').parent().remove();
                                        //mapPagingType == 'infinite-scroll'.
                                        window.removeEventListener('scroll', mapInfiniteScroll);
                                    }
                                    //Update next page url.
                                    mapNextPageUrl = mapNextPageUrl.replace(/\/page\/[0-9]*/, '/page/'+ mapNextPageNumber);
                                }

                                if (mapPagingType == 'pagination') {                                  
                                    //Remove previously existing posts.
                                    $mapExistingPosts.remove();
                                    //Update URL and store history for browser back/forward buttons
                                    history.pushState(null, null, mapNextPageUrl);
                                    //Find the new navigation and update, active state, next and prev buttons.
                                    var $mapNewPagination = $(mapResponse).find(mapPaginationClass);
                                    $(mapPaginationClass).replaceWith($mapNewPagination);
                                }

                                //Remove loading div and clear timer.
                                mapLoaded();

                            },
            error:          function(req, status) {
                                //Oops.
                                mapFailed();
                            },
            complete:       function() {
                                if (mapPagingType == 'pagination') {
                                    $('body,html').animate({
                                        scrollTop: $(mapPostsWrapperClass).offset().top - 150
                                    }, 400);
                                }
                            }                            
        });
    };


    /**
     * Add loader.gif div.
     * Use last() as some themes don't wrap navigation and this only adds loader.gif div once.
     */
    var mapAddLoader = function() {
        $(mapPaginationClass).last().before('<div class="malinky-ajax-paging-loading">' + mapAjaxLoader + '</div>');     
    };


    /**
     * While new posts are loaded.
     * Show loader.gif.
     * Add loading text to button if condition is true.
     * This function is called using a setTimeout of 750 in the click event handler.
     */
    var mapLoading = function() {
        $('.malinky-ajax-paging-loading').show();
        if (mapPagingType == 'load-more' || mapPagingType == 'infinite-scroll') {
            $('#malinky-ajax-paging-button').text(mapLoadingMorePostsText);
        }        
    };


    /**
     * After new posts have been loaded.
     * Hide loader.gif.
     * Add loading text to button if condition is true.
     * Clear timer.
     */
    var mapLoaded = function() {        
        $('.malinky-ajax-paging-loading').hide();        
        if (mapPagingType == 'load-more' || mapPagingType == 'infinite-scroll') {
            $('#malinky-ajax-paging-button').text(mapLoadMoreButtonText);
        }
        clearTimeout(mapLoadingTimer);
    };


    /**
     * Called if AJAX error.
     */
    var mapFailed = function() {        
        $('.malinky-ajax-paging-loading').hide();        
        clearTimeout(mapLoadingTimer);
    };    


    /**
     * Infinite scroll called with debounce.
     */
    var mapInfiniteScroll = debounce(function() {
        //After scroll calculate the number of pixels still hidden off the bottom of the screen.
        var mapContentPixelsToDocBottom = $(document).height() - $(window).scrollTop() - $(window).height();
        //(Is number of pixels hidden off bottom of screen minus the buffer) less than (the top position of the nav in relation to the bottom of the doc).
        if (mapContentPixelsToDocBottom - mapInfiniteScrollBuffer < mapPaginationClassPixelsToDocBottom) {
            //Delay loading text and div.
            mapLoadingTimer = setTimeout(mapLoading, 750);
            //Load more posts.
            mapLoadPosts();
            /**
             * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
             * setTimeout(mapLoadPosts, 3000);
             */       
        }
    }, 250);


    /**
     * Debounce.
     */
    function debounce (func, wait, immediate) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if (!immediate) func.apply(context, args);
            };
            var callNow = immediate && !timeout;
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
            if (callNow) func.apply(context, args);
        };
    };


    //Start.
    init();

})(jQuery);