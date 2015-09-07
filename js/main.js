var MalinkyAjaxPaging = ( function( $ ) {

    // Check posts_wrapper, post_wrapper, pagination_wrapper and pagination_wrapper exist on the page.
    // If not then loop onto the next key in malinkySettings.
    for ( var key in malinkySettings ) {
        if ($( malinkySettings[key].posts_wrapper ).length && 
            $( malinkySettings[key].post_wrapper ).length && 
            $( malinkySettings[key].pagination_wrapper ).length && 
            $( malinkySettings[key].pagination_wrapper ).length ) {
            
            // Variables.
            // max_num_pages, next_page_number, next_page_url aren't part of the settings array.
            var mapAjaxLoader                       = malinkySettings[key].ajax_loader,
                mapCssLoadMore                      = malinkySettings[key].malinky_load_more,
                mapCssLoadMoreButton                = malinkySettings[key].malinky_load_more_button,
                mapInfiniteScrollBuffer             = parseInt( malinkySettings[key].infinite_scroll_buffer ),
                mapLoadingTimer                     = '',
                mapLoadingMorePostsText             = malinkySettings[key].loading_more_posts_text,
                mapLoadMoreButtonText               = malinkySettings[key].load_more_button_text,
                mapPaginationClass                  = malinkySettings[key].pagination_wrapper,
                mapPaginationClassPixelsToDocBottom = $( document ).height() - $( mapPaginationClass ).offset().top,
                mapPagingType                       = malinkySettings[key].paging_type,
                mapPostsWrapperClass                = malinkySettings[key].posts_wrapper,
                mapPostClass                        = malinkySettings[key].post_wrapper,
                mapMaxNumPages                      = parseInt( malinkySettings.max_num_pages ),
                mapNextPageNumber                   = parseInt( malinkySettings.next_page_number ),
                mapNextPageSelector                 = malinkySettings[key].next_page_selector,
                mapNextPageUrl                      = $( malinkySettings[key].next_page_selector ).attr( 'href' ) || malinkySettings.next_page_url;
        }
    }

    /**
     * Initialize.
     */
    var init = function() {

        if ( mapPagingType == 'infinite-scroll' ) {
            
            // Add loader.gif div.
            mapAddLoader();

            // Remove existing pagination.
            $( mapPaginationClass ).remove();

            // Attach scroll event listener to the window.
            // See mapInfiniteScroll function.
            window.addEventListener( 'scroll', mapInfiniteScroll );

        } else if ( mapPagingType == 'load-more' ) {

            // Add new pagination button after last mapPaginationClass.
            // Use last() as some themes don't wrap navigation and this only adds loader.gif div once.
            $( mapPaginationClass ).last().after('<div class="malinky-load-more"><a href="' + mapNextPageUrl + '" id="malinky-ajax-pagination-button" class="malinky-load-more__button">' + mapLoadMoreButtonText + '</a></div>');

            // Add loader.gif div.
            mapAddLoader();

            // Remove the existing pagination.
            // Search for mapPaginationClass but don't remove if child contains #malinky-ajax-pagination-button.
            // This would be the new navigation if the user has set a css class the same as the original mapPaginationClass.
            $( mapPaginationClass + ':not(:has(>a#malinky-ajax-pagination-button))' ).remove();
            
            // Attach a click event handler to the new pagination button.
            // Doesn't use delegated event as this click event is added after the new pagination button is added to the dom.
            $( '#malinky-ajax-pagination-button').click( function( event ) {
                event.preventDefault();

                // Delay loading text and div.
                mapLoadingTimer = setTimeout( mapLoading, 750 );
                
                // Load more posts.
                mapLoadPosts();
                
                /**
                 * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
                 * setTimeout(mapLoadPosts, 3000);
                 */
            }); 

        } else if ( mapPagingType == 'pagination' ) {

            // Add loader.gif div.
            mapAddLoader();

            /**
             * Attach a click event handler to the pagination links.
             * The pagination class is reloaded after a page change to update the page numbers therefore a delegated event is used.
             * This is attached to the document as it's the only item we can be sure to be there on first page load.
             * @link http://api.jquery.com/on/
             */
            $( document ).on( 'click', mapPaginationClass, function( event ) {
                event.preventDefault();

                // Delay loading text and div.
                mapLoadingTimer = setTimeout( mapLoading, 750);

                // Get the url of the clicked link.
                mapNextPageUrl = event.target.href;

                // Load more posts.
                mapLoadPosts();

                /**
                 * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
                 * setTimeout(mapLoadPosts, 3000);
                 */
            });

            // Attach popstate event listener which is triggered on the click of the browser back button.
            // The url generated by the browser back/forward button is saved in mapNextPageUrl and mapLoadPosts called.
            window.addEventListener( 'popstate', function( event ) {
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
                success:    function( response ) {

                                // Parse HTML first.
                                var mapResponse = $.parseHTML( response );

                                // Find the posts from the full html response using mapPostClass.
                                var $mapLoadedPosts = $( mapResponse ).find( mapPostClass );

                                // jQuery object of the last displayed post.
                                var $mapInsertPoint = $( mapPostsWrapperClass + ' ' + mapPostClass ).last();

                                // Save the existing posts if they are to be removed after insertion when mapPagingType == 'pagination'.
                                var $mapExistingPosts = $( mapPostClass );

                                // Insert the posts after the last currently displayed post.
                                $mapInsertPoint.after( $mapLoadedPosts );
                                
                                if ( mapPagingType == 'infinite-scroll' || mapPagingType == 'load-more' ) {
                                    // Increment page number.
                                    mapNextPageNumber++;

                                    // If we're on the last page and all posts have been loaded.
                                    if (mapNextPageNumber > mapMaxNumPages ) {
                                        // mapPagingType == 'load-more'.
                                        $( '#malinky-ajax-pagination-button' ).parent().remove();

                                        // mapPagingType == 'infinite-scroll'.
                                        window.removeEventListener( 'scroll', mapInfiniteScroll );
                                    }
                                    // Update next page url.
                                    mapNextPageUrl = mapNextPageUrl.replace( /\/page\/[0-9]*/, '/page/'+ mapNextPageNumber );
                                }

                                if ( mapPagingType == 'pagination' ) {                                  
                                    // Remove previously existing posts.
                                    $mapExistingPosts.remove();

                                    // Update URL and store history for browser back/forward buttons.
                                    history.pushState( null, null, mapNextPageUrl );

                                    // Find the new navigation and update, active state, next and prev buttons.
                                    var $mapNewPagination = $( mapResponse ).find( mapPaginationClass );
                                    $( mapPaginationClass ).replaceWith( $mapNewPagination );
                                }

                                // Remove loading div and clear timer.
                                mapLoaded();

                            },
            error:          function( req, status ) {
                                //Oops.
                                mapFailed();
                            },
            complete:       function() {
                                if ( mapPagingType == 'pagination' ) {
                                    $( 'body,html' ).animate({
                                        scrollTop: $( mapPostsWrapperClass ).offset().top - 150
                                    }, 400 );
                                }
                            }                            
        });
    };

    /**
     * Add loader.gif div.
     * Use last() as some themes don't wrap navigation and this only adds loader.gif div once.
     */
    var mapAddLoader = function() {
        $( mapPaginationClass ).last().before( '<div class="malinky-ajax-pagination-loading">' + mapAjaxLoader + '</div>' );     
    };

    /**
     * While new posts are loaded.
     * Show loader.gif.
     * Add loading text to button if condition is true.
     * This function is called using a setTimeout of 750 in the click event handler.
     */
    var mapLoading = function() {
        $( '.malinky-ajax-pagination-loading' ).show();
        if ( mapPagingType == 'load-more' || mapPagingType == 'infinite-scroll' ) {
            $( '#malinky-ajax-pagination-button' ).text( mapLoadingMorePostsText );
        }        
    };

    /**
     * After new posts have been loaded.
     * Hide loader.gif.
     * Add loading text to button if condition is true.
     * Clear timer.
     */
    var mapLoaded = function() {        
        $( '.malinky-ajax-pagination-loading' ).hide();        
        if ( mapPagingType == 'load-more' || mapPagingType == 'infinite-scroll' ) {
            $( '#malinky-ajax-pagination-button' ).text( mapLoadMoreButtonText );
        }
        clearTimeout( mapLoadingTimer );
    };

    /**
     * Called if AJAX error.
     */
    var mapFailed = function() {        
        $( '.malinky-ajax-pagination-loading' ).hide();        
        clearTimeout( mapLoadingTimer );
    };    

    /**
     * Infinite scroll called with debounce.
     */
    var mapInfiniteScroll = debounce( function() {
        // After scroll calculate the number of pixels still hidden off the bottom of the screen.
        var mapContentPixelsToDocBottom = $( document ).height() - $( window ).scrollTop() - $( window ).height();

        // (Is number of pixels hidden off bottom of screen minus the buffer) less than (the top position of the nav in relation to the bottom of the doc).
        if ( mapContentPixelsToDocBottom - mapInfiniteScrollBuffer < mapPaginationClassPixelsToDocBottom ) {
            // Delay loading text and div.
            mapLoading();

            // Load more posts.
            mapLoadPosts();

            /**
             * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
             * setTimeout(mapLoadPosts, 3000);
             */       
        }
    }, 250 );

    /**
     * Debounce.
     */
    function debounce ( func, wait, immediate ) {
        var timeout;
        return function() {
            var context = this, args = arguments;
            var later = function() {
                timeout = null;
                if ( ! immediate ) func.apply( context, args );
            };
            var callNow = immediate && !timeout;
            clearTimeout( timeout );
            timeout = setTimeout( later, wait );
            if ( callNow ) func.apply( context, args );
        };
    };

    // Start.
    init();

})(jQuery);