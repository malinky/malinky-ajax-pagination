var MalinkyAjaxPaging = ( function( $ ) {

    /**
     * Initialize.
     */
    var init = function() {

        // Local variables for each AjaxPaging.
        var mymapAjaxLoader                       = mapAjaxLoader,
            mymapCssLoadMore                      = mapCssLoadMore,
            mymapCssLoadMoreButton                = mapCssLoadMoreButton,
            mymapInfiniteScrollBuffer             = mapInfiniteScrollBuffer,
            mymapLoadingTimer                     = '',
            mymapLoadingMorePostsText             = mapLoadingMorePostsText,
            mymapLoadMoreButtonText               = mapLoadMoreButtonText,
            mymapPaginationClass                  = mapPaginationClass,
            mymapPaginationClassPixelsToDocBottom = mapPaginationClassPixelsToDocBottom,
            mymapPagingType                       = mapPagingType,
            mymapPostsWrapperClass                = mapPostsWrapperClass,
            mymapPostClass                        = mapPostClass,
            mymapMaxNumPages                      = mapMaxNumPages,
            mymapNextPageNumber                   = mapNextPageNumber,
            mymapNextPageSelector                 = mapNextPageSelector,
            mymapNextPageUrl                      = mapNextPageUrl,
            mymapPaginatorCount                   = mapPaginatorCount,
            mymapUserCallback                     = mapUserCallback,
            infiniteScrollRunning                 = false;

        /**
        * Load new posts and append to or replace existing posts depending on paging_type.
        */
        var mapLoadPosts = function () {
            $.ajax({
                    type:       'GET',
                    url:        mymapNextPageUrl,
                    dataType:   'html',
                    success:    function( response ) {

                                    // Parse HTML first.
                                    var mapResponse = $.parseHTML( response );

                                    // Find the post wrappers.
                                    var $postsWrapperClass = $( mapResponse ).find( mymapPostsWrapperClass );

                                    // Add data attribute count to each of the posts wrapper in the response so they match with the originals.
                                    // Add data attribute count to each of the pagination classes in the response so they match with the originals.
                                    
                                    // Counter for the data attributes in the response.
                                    var paginatorCountAjax = 1;
                                    var paginatorTotalCountAjax = 0;

                                    // Determine the total number of paginations on the page.
                                    // See bottom of script.
                                    for ( var key in malinkySettings ) {
                                        if ($( mapResponse ).find( malinkySettings[key].posts_wrapper ).length && 
                                            $( mapResponse ).find( malinkySettings[key].post_wrapper ).length && 
                                            $( mapResponse ).find( malinkySettings[key].pagination_wrapper ).length ) {
                                                paginatorTotalCountAjax++;
                                        }
                                    }

                                    // See bottom of script.
                                    for ( var key in malinkySettings ) {
                                        // console.log(malinkySettings[key].posts_wrapper);
                                        // console.log(malinkySettings[key].post_wrapper);
                                        // console.log(malinkySettings[key].pagination_wrapper);
                                        // console.log(malinkySettings[key].next_page_selector);
                                        // console.log($( mapResponse ).find( malinkySettings[key].posts_wrapper ).length);
                                        // console.log($( mapResponse ).find( malinkySettings[key].post_wrapper ).length);
                                        // console.log($( mapResponse ).find( malinkySettings[key].pagination_wrapper ).length);
                                        // console.log($( mapResponse ).find( malinkySettings[key].next_page_selector ).length);
                                        // Don't check for .next_page_selector as it won't exist if paging into the last page.
                                        if ($( mapResponse ).find( malinkySettings[key].posts_wrapper ).length && 
                                            $( mapResponse ).find( malinkySettings[key].post_wrapper ).length && 
                                            $( mapResponse ).find( malinkySettings[key].pagination_wrapper ).length ) {
                                            // Single pagination on the page.
                                            if ( paginatorTotalCountAjax == 1 ) {
                                                $( mapResponse ).find( malinkySettings[key].posts_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                                                $( mapResponse ).find( malinkySettings[key].pagination_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                                            // Multiple paginations on the page.
                                            // posts_wrapper must be unique.
                                            } else {
                                                $( mapResponse ).find( malinkySettings[key].posts_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                                                $( mapResponse ).find( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].pagination_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                                                // Set up a data attribute on the the next page selector generally a.next.
                                                $( mapResponse ).find( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].next_page_selector ).attr( 'data-paginator-count', paginatorCountAjax );
                                                paginatorCountAjax++;
                                            }
                                        }
                                    }

                                    // Find the posts from the full html response using mymapPostClass.
                                    var $mapLoadedPosts = $( mapResponse ).find( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass );

                                    // jQuery object of the last displayed post.
                                    var $mapInsertPoint = $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass ).last();

                                    // Save the existing posts if they are to be removed after insertion when mymapPagingType == 'pagination'.
                                    var $mapExistingPosts = $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass );

                                    // Insert the posts after the last currently displayed post.
                                    $mapInsertPoint.after( $mapLoadedPosts );
                                
                                    if ( mymapPagingType == 'infinite-scroll' || mymapPagingType == 'load-more' ) {

                                        // Increment page number.
                                        mymapNextPageNumber++;

                                        // Single pagination on the page.
                                        if ( paginatorTotalCountAjax == 1 ) {
                                            // If we're on the last page and all posts have been loaded.
                                            if (mymapNextPageNumber > mymapMaxNumPages ) {
                                                // mymapPagingType == 'load-more'.
                                                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).parent().remove();
                                                
                                                // mymapPagingType == 'infinite-scroll'.
                                                window.removeEventListener( 'scroll', mapInfiniteScroll );
                                            }

                                            // Update next page url.
                                            mymapNextPageUrl = mymapNextPageUrl.replace( /\/page\/[0-9]*/, '/page/'+ mymapNextPageNumber );
                                        // Multiple paginations on the page.
                                        } else {
                                            // If the next page href is undefined this means there is no next page, generally the a.next element.
                                            if ( ! $( mapResponse ).find( mymapNextPageSelector + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).attr( 'href' ) ) {
                                                // mymapPagingType == 'load-more'.
                                                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).parent().remove();

                                                // mymapPagingType == 'infinite-scroll'.
                                                window.removeEventListener( 'scroll', mapInfiniteScroll );
                                            }

                                            // Update next page url.
                                            mymapNextPageUrl = $( mapResponse ).find( mymapNextPageSelector + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).attr( 'href' );
                                        }
                                    }

                                    if ( mymapPagingType == 'pagination' ) {
                                        // Remove previously existing posts.
                                        $mapExistingPosts.remove();

                                        // Update URL and store history for browser back/forward buttons.
                                        history.pushState( null, null, mymapNextPageUrl );

                                        // Find the new navigation and update, active state, next and prev buttons.
                                        // Use first to ensure pages with top and bottom pagination doesn't replace them twice.
                                        var $mapNewPagination = $( mapResponse ).find( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).first();
                                        $( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).replaceWith( $mapNewPagination );
                                    }

                                    // Remove loading div and clear timer.
                                    mapLoaded();

                                },
                error:          function( req, status ) {
                                    //Oops.
                                    mapFailed();
                                },
                complete:       function() {
                                    if ( mymapPagingType == 'pagination' ) {
                                        $( 'body,html' ).animate({
                                            scrollTop: $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).offset().top - 150
                                        }, 400 );
                                    }
                                    if ( mymapPagingType == 'infinite-scroll' ) {
                                        infiniteScrollRunning = false;
                                    }
                                    // User callback
                                    eval(mymapUserCallback);
                                }                            
            });
        };

        /**
         * Add loader.gif div.
         * Use last() as some themes don't wrap navigation and this only adds loader.gif div once.
         * 
         * MALINKY - Issue above now as removed last() need sample theme where navigation isn't wrapped.
         */
        var mapAddLoader = function() {
            $( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).last().before( '<div class="malinky-ajax-pagination-loading" data-paginator-count="' + mymapPaginatorCount + '">' + mymapAjaxLoader + '</div>' );     
        };

        /**
         * While new posts are loaded.
         * Show loader.gif.
         * Add loading text to button if condition is true.
         * This function is called using a setTimeout of 750 in the click event handler.
         */
        var mapLoading = function() {
            $( '.malinky-ajax-pagination-loading[data-paginator-count="' + mymapPaginatorCount + '"]' ).show();
            if ( mymapPagingType == 'load-more' || mymapPagingType == 'infinite-scroll' ) {
                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).text( mymapLoadingMorePostsText );
            }
        };

        /**
         * After new posts have been loaded.
         * Hide loader.gif.
         * Add loading text to button if condition is true.
         * Clear timer.
         */
        var mapLoaded = function() {
            $( '.malinky-ajax-pagination-loading[data-paginator-count="' + mymapPaginatorCount + '"]' ).hide();        
            if ( mymapPagingType == 'load-more' || mymapPagingType == 'infinite-scroll' ) {
                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).text( mymapLoadMoreButtonText );
            }
            clearTimeout( mapLoadingTimer );
        };

        /**
         * Called if AJAX error.
         */
        var mapFailed = function() {        
            $( '.malinky-ajax-pagination-loading[data-paginator-count="' + mymapPaginatorCount + '"]' ).hide();        
            clearTimeout( mapLoadingTimer );
        };

        /**
         * Infinite scroll called with debounce.
         */
        var mapInfiniteScroll = debounce( function() {
            if (infiniteScrollRunning) return;
            
            // After scroll calculate the number of pixels still hidden off the bottom of the screen.
            var mapContentPixelsToDocBottom = $( document ).height() - $( window ).scrollTop() - $( window ).height();

            var postsWrapperClassOffset = $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).offset().top;
            var postsWrapperClassHeight = $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).outerHeight();

            if ( ( $(window).height() + $(window).scrollTop() + mymapInfiniteScrollBuffer ) > ( postsWrapperClassOffset + postsWrapperClassHeight ) ) {
                // We're scrolling
                infiniteScrollRunning = true;

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
        
        if ( mymapPagingType == 'infinite-scroll' ) {

            // If the next page href is undefined this means there is no next page.
            // This is removed this way to be used for multiple pagination.
            if ( $( mymapNextPageSelector + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).attr( 'href' ) ) {

                // Add loader.gif div.
                mapAddLoader();

                // Remove existing pagination.
                $( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).remove();

                // Attach scroll event listener to the window.
                // See mapInfiniteScroll function.
                window.addEventListener( 'scroll', mapInfiniteScroll );

            }

        } else if ( mymapPagingType == 'load-more' ) {

            // If the next page href is undefined this means there is no next page.
            // This is removed this way to be used for multiple pagination.
            if ( $( mymapNextPageSelector + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).attr( 'href' ) ) {

                // Add new pagination button after last mymapPaginationClass.
                // Use last() as some themes don't wrap navigation and this only adds loader.gif div once.
                $( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).last().after('<div class="malinky-load-more"><a href="' + mymapNextPageUrl + '" id="malinky-ajax-pagination-button" class="malinky-load-more__button" data-paginator-count="' + mymapPaginatorCount + '">' + mapLoadMoreButtonText + '</a></div>');

                // Add loader.gif div.
                mapAddLoader();

                // Remove the existing pagination.
                // Search for mymapPaginationClass but don't remove if child contains #malinky-ajax-pagination-button.
                // This would be the new navigation if the user has set a css class the same as the original mymapPaginationClass.
                $( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ':not(:has(>a#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]))' ).remove();
                
                // Attach a click event handler to the new pagination button.
                // Doesn't use delegated event as this click event is added after the new pagination button is added to the dom.
                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).click( function( event ) {
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

            }

        } else if ( mymapPagingType == 'pagination' ) {

            // Add loader.gif div.
            mapAddLoader();

            /**
             * Attach a click event handler to the pagination links.
             * The pagination class is reloaded after a page change to update the page numbers therefore a delegated event is used.
             * This is attached to the document as it's the only item we can be sure to be there on first page load.
             * @link http://api.jquery.com/on/
             */
            $( document ).on( 'click', mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]', function( event ) {
                event.preventDefault();

                // Delay loading text and div.
                mapLoadingTimer = setTimeout( mapLoading, 750);

                // Get the url of the clicked link.
                mymapNextPageUrl = event.target.href;

                mapLoadPosts();

                /**
                 * Debug timer. Remove mapLoadPosts call and use setTimeout instead.
                 * setTimeout(mapLoadPosts, 3000);
                 */
            });

            // Attach popstate event listener which is triggered on the click of the browser back button.
            // The url generated by the browser back/forward button is saved in mymapNextPageUrl and mapLoadPosts called.
            window.addEventListener( 'popstate', function( event ) {
                mymapNextPageUrl = document.URL;
                mapLoadPosts();
            });

        }
    };

    // Check posts_wrapper, post_wrapper, pagination_wrapper and pagination_wrapper exist on the page.
    // If not then loop onto the next key in malinkySettings.
    // Iterating while there are still others.
    var paginatorCount = 0;
    // Used to set up the data attributes.
    var paginatorCountSetUp = 1;
    // The total count of paginations on the page.
    var paginatorTotalCount = 0;

    // Determine the total number of paginations on the page.
    // If there is only one then we can find the mapPaginationClass anywhere on the page
    // ensuring we keep backward compatability from v1.0.0 to v1.1.0.
    // If there are multiple the user needs to ensure the mapPaginationClass is a child of the mapPostsWrapperClass.
    // paginatorTotalCount is used throughout to determine this.
    for ( var key in malinkySettings ) {
        if ($( malinkySettings[key].posts_wrapper ).length && 
            $( malinkySettings[key].post_wrapper ).length && 
            $( malinkySettings[key].pagination_wrapper ).length && 
            $( malinkySettings[key].next_page_selector ).length ) {
                paginatorTotalCount++;
        }
    }

    for ( var key in malinkySettings ) {
        if ($( malinkySettings[key].posts_wrapper ).length && 
            $( malinkySettings[key].post_wrapper ).length && 
            $( malinkySettings[key].pagination_wrapper ).length && 
            $( malinkySettings[key].next_page_selector ).length ) {
            
            // Add data attribute count to each of the posts wrapper.
            // Add data attribute count to each of the pagination classes.
            
            // Single pagination on the page.
            if ( paginatorTotalCount == 1 ) {
                $( malinkySettings[key].posts_wrapper ).attr( 'data-paginator-count', paginatorCountSetUp );
                $( malinkySettings[key].pagination_wrapper ).attr( 'data-paginator-count', paginatorCountSetUp );
                $( malinkySettings[key].next_page_selector ).attr( 'data-paginator-count', paginatorCountSetUp );
            // Multiple paginations on the page.
            // posts_wrapper must be unique.
            } else {
                $( malinkySettings[key].posts_wrapper ).attr( 'data-paginator-count', paginatorCountSetUp );
                $( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].pagination_wrapper ).attr( 'data-paginator-count', paginatorCountSetUp );
                $( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].next_page_selector ).attr( 'data-paginator-count', paginatorCountSetUp );
                paginatorCountSetUp++;
            }

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
                mapPaginatorCount                   = ++paginatorCount,
                mapUserCallback                     = malinkySettings[key].callback_function;

            // If there is only one pagination we can find the next_page_selector anywhere on the page.
            if ( paginatorTotalCount == 1 ) {
                // Would like to remove the next_page_url altogether and force this to be set in the navigation href.
                var mapNextPageUrl = $( malinkySettings[key].next_page_selector ).attr( 'href' ) || malinkySettings.next_page_url;
            // Otherwise it should be a child of posts wrapper.
            // This also allows the settings to be added into the admin in any order.
            } else {
                // Would like to remove the next_page_url altogether and force this to be set in the navigation href.
                var mapNextPageUrl = $( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].next_page_selector ).attr( 'href' ) || malinkySettings.next_page_url;
            }
            
            // Start.
            init();
        }
    }

})(jQuery);