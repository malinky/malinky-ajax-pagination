var MalinkyAjaxPaging = ( function( $ ) {

    /**
     * Initialize.
     */
    var init = function(mapVars) {

        // Local variables for each AjaxPaging.
        var mymapAjaxLoader                       = mapVars.mapAjaxLoader,
            mymapCssLoadMore                      = mapVars.mapCssLoadMore,
            mymapCssLoadMoreButton                = mapVars.mapCssLoadMoreButton,
            mymapInfiniteScrollBuffer             = mapVars.mapInfiniteScrollBuffer,
            mymapLoadingTimer                     = '',
            mymapLoadingMorePostsText             = mapVars.mapLoadingMorePostsText,
            mymapLoadMoreButtonText               = mapVars.mapLoadMoreButtonText,
            mymapPaginationClass                  = mapVars.mapPaginationClass,
            mymapPaginationClassPixelsToDocBottom = mapVars.mapPaginationClassPixelsToDocBottom,
            mymapPagingType                       = mapVars.mapPagingType,
            mymapPostsWrapperClass                = mapVars.mapPostsWrapperClass,
            mymapPostClass                        = mapVars.mapPostClass,
            mymapNextPageSelector                 = mapVars.mapNextPageSelector,
            mymapNextPageUrl                      = mapVars.mapNextPageUrl,
            mymapPaginatorCount                   = mapVars.mapPaginatorCount,
            mymapUserCallback                     = mapVars.mapUserCallback,
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

                                    // Determine the total number of paginations on the page.
                                    var paginatorTotalCountAjax = mapPaginatorTotalCount(mapResponse);

                                    // Add paginator counts to the ajax reponse.
                                    mapAddPaginatorCount(mapResponse, paginatorTotalCountAjax);

                                    // Find the posts from the full html response using mymapPostClass.
                                    var $mapLoadedPosts = $( mapResponse ).find( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass );

                                    // jQuery object of the last displayed post.
                                    var $mapInsertPoint = $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass ).last();

                                    // Save the existing posts if they are to be removed after insertion when mymapPagingType == 'pagination'.
                                    var $mapExistingPosts = $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass );

                                    // Insert the posts after the last currently displayed post.
                                    $mapInsertPoint.after( $mapLoadedPosts );
                                
                                    if ( mymapPagingType == 'infinite-scroll' || mymapPagingType == 'load-more' ) {
                                        // Single pagination on the page.
                                        if ( paginatorTotalCountAjax == 1 ) {
                                            // If on last page.
                                            if ( ! mapIsLastPage( mapResponse, mymapNextPageSelector ) ) {
                                                // mymapPagingType == 'load-more'.
                                                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).parent().remove();
                                                // mymapPagingType == 'infinite-scroll'.
                                                window.removeEventListener( 'scroll', mapInfiniteScroll );
                                            // Or get next page url.
                                            } else {
                                                mymapNextPageUrl = $( mapResponse ).find( mymapNextPageSelector ).attr( 'href' );
                                            }
                                        // Multiple paginations on the page.
                                        } else {
                                            // If on last page.
                                            if ( ! mapIsLastPage( mapResponse, mymapNextPageSelector + '[data-paginator-count="' + mymapPaginatorCount + '"]' ) ) {
                                                // mymapPagingType == 'load-more'.
                                                $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).parent().remove();
                                                // mymapPagingType == 'infinite-scroll'.
                                                window.removeEventListener( 'scroll', mapInfiniteScroll );
                                            // Or get next page url.
                                            } else {                                                
                                                mymapNextPageUrl = $( mapResponse ).find( mymapNextPageSelector + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).attr( 'href' );
                                            }
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
                complete:       function(requestObj) {
                                    if ( mymapPagingType == 'pagination' ) {
                                        $( 'body,html' ).animate({
                                            scrollTop: $( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).offset().top - 150
                                        }, 400 );
                                    }
                                    if ( mymapPagingType == 'infinite-scroll' ) {
                                        infiniteScrollRunning = false;
                                    }
                                    if ( mymapPagingType == 'load-more' ) {
                                        $( '#malinky-ajax-pagination-button[data-paginator-count="' + mymapPaginatorCount + '"]' ).removeClass('malinky-load-more__button-disable');
                                    }

                                    // Parse HTML first.
                                    var mapResponse = $.parseHTML( requestObj.responseText );

                                    // Determine the total number of paginations on the page.
                                    var paginatorTotalCountAjax = mapPaginatorTotalCount(mapResponse);

                                    // Add paginator counts to the ajax reponse.
                                    mapAddPaginatorCount(mapResponse, paginatorTotalCountAjax);

                                    // Find the posts from the full html response using mymapPostClass.
                                    var $mapLoadedPosts = $( mapResponse ).find( mymapPostsWrapperClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' + ' ' + mymapPostClass );

                                    // User callback.
                                    // An array of new posts.
                                    // The current URL.
                                    (function(loadedPosts, url) {
                                        eval(mymapUserCallback);
                                    })($mapLoadedPosts, this.url);
                                }
            });
        };

        /**
         * Determine the total number of paginations on the page.
         * See bottom of script.
         *
         * @param str repsone Full html response
         * @return int
         */
        var mapPaginatorTotalCount = function(response) {
            var paginatorTotalCountAjax = 0;

            for ( var key in malinkySettings ) {
                if ($( response ).find( malinkySettings[key].posts_wrapper ).length && 
                    $( response ).find( malinkySettings[key].post_wrapper ).length && 
                    $( response ).find( malinkySettings[key].pagination_wrapper ).length ) {
                        paginatorTotalCountAjax++;
                }
            }

            return paginatorTotalCountAjax;
        }

        /**
         * Add paginator counts to the ajax reponse.
         *
         * @param str repsone Full html response
         * @param int paginatorTotalCount Total paginations on the page.
         * return void
         */
        var mapAddPaginatorCount = function(response, paginatorTotalCount) {
            // Counter for the data attributes in the response.
            var paginatorCountAjax = 1;

            // See bottom of script.
            for ( var key in malinkySettings ) {
                // console.log(malinkySettings[key].posts_wrapper);
                // console.log(malinkySettings[key].post_wrapper);
                // console.log(malinkySettings[key].pagination_wrapper);
                // console.log(malinkySettings[key].next_page_selector);
                // console.log($( response ).find( malinkySettings[key].posts_wrapper ).length);
                // console.log($( response ).find( malinkySettings[key].post_wrapper ).length);
                // console.log($( response ).find( malinkySettings[key].pagination_wrapper ).length);
                // console.log($( response ).find( malinkySettings[key].next_page_selector ).length);
                // Don't check for .next_page_selector as it won't exist if paging into the last page.
                if ($( response ).find( malinkySettings[key].posts_wrapper ).length && 
                    $( response ).find( malinkySettings[key].post_wrapper ).length && 
                    $( response ).find( malinkySettings[key].pagination_wrapper ).length ) {
                    // Single pagination on the page.
                    if ( paginatorTotalCount == 1 ) {
                        $( response ).find( malinkySettings[key].posts_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                        $( response ).find( malinkySettings[key].pagination_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                    // Multiple paginations on the page.
                    // posts_wrapper must be unique.
                    } else {
                        $( response ).find( malinkySettings[key].posts_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                        $( response ).find( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].pagination_wrapper ).attr( 'data-paginator-count', paginatorCountAjax );
                        // Set up a data attribute on the the next page selector generally a.next.
                        $( response ).find( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].next_page_selector ).attr( 'data-paginator-count', paginatorCountAjax );
                        paginatorCountAjax++;
                    }
                }
            }
        }

        /**
         * Check if response is the last page.
         * 
         * @param str response Full html response
         * @param str nextPageSelector
         * @return int Use 0 length as falsey.
         */
        var mapIsLastPage = function(response, nextPageSelector) {
            return $( response ).find( nextPageSelector ).length;
        }

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
            clearTimeout( mymapLoadingTimer );
        };

        /**
         * Called if AJAX error.
         */
        var mapFailed = function() {        
            $( '.malinky-ajax-pagination-loading[data-paginator-count="' + mymapPaginatorCount + '"]' ).hide();        
            clearTimeout( mymapLoadingTimer );
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
                $( mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"]' ).last().after('<div class="malinky-load-more"><a href="' + mymapNextPageUrl + '" id="malinky-ajax-pagination-button" class="malinky-load-more__button" data-paginator-count="' + mymapPaginatorCount + '">' + mymapLoadMoreButtonText + '</a></div>');

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

                    // Disable button (anchor) so can't be clicked twice.
                    $(this).addClass('malinky-load-more__button-disable');

                    // Delay loading text and div.
                    mymapLoadingTimer = setTimeout( mapLoading, 750 );
                    
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
             * A handler is added to each <a>. This means we can use this as the event.currentTarget to select the href.
             * Otherwise users may nest elements like arrows inside the <a> which could have triggered the event and
             * would be the event.target instead.
             * The pagination class is reloaded after a page change to update the page numbers therefore a delegated event is used.
             * This is attached to the document as it's the only item we can be sure to be there on first page load.
             * @link http://api.jquery.com/on/
             */
            $( document ).on( 'click', mymapPaginationClass + '[data-paginator-count="' + mymapPaginatorCount + '"] a', function( event ) {
                event.preventDefault();

                // Delay loading text and div.
                mymapLoadingTimer = setTimeout( mapLoading, 750);

                // Get the url of the clicked link.
                mymapNextPageUrl = event.currentTarget.href;

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

    var setUp = function() {

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
                var mapVars = {
                    mapAjaxLoader                       : malinkySettings[key].ajax_loader,
                    mapCssLoadMore                      : malinkySettings[key].malinky_load_more,
                    mapCssLoadMoreButton                : malinkySettings[key].malinky_load_more_button,
                    mapInfiniteScrollBuffer             : parseInt( malinkySettings[key].infinite_scroll_buffer ),
                    mapLoadingTimer                     : '',
                    mapLoadingMorePostsText             : malinkySettings[key].loading_more_posts_text,
                    mapLoadMoreButtonText               : malinkySettings[key].load_more_button_text,
                    mapPaginationClass                  : malinkySettings[key].pagination_wrapper,
                    mapPagingType                       : malinkySettings[key].paging_type,
                    mapPostsWrapperClass                : malinkySettings[key].posts_wrapper,
                    mapPostClass                        : malinkySettings[key].post_wrapper,
                    mapNextPageSelector                 : malinkySettings[key].next_page_selector,
                    mapPaginatorCount                   : ++paginatorCount,
                    mapUserCallback                     : malinkySettings[key].callback_function
                };

                mapVars.mapPaginationClassPixelsToDocBottom = jQuery( document ).height() - jQuery( mapVars.mapPaginationClass ).offset().top;

                // If there is only one pagination we can find the next_page_selector anywhere on the page.
                if ( paginatorTotalCount == 1 ) {
                    mapVars.mapNextPageUrl = $( malinkySettings[key].next_page_selector ).attr( 'href' );
                // Otherwise it should be a child of posts wrapper.
                // This also allows the settings to be added into the admin in any order.
                } else {
                    mapVars.mapNextPageUrl = $( malinkySettings[key].posts_wrapper + ' ' + malinkySettings[key].next_page_selector ).attr( 'href' );
                }
                
                // Init each match.
                init(mapVars);
            }
        }

    }

    // Start.
    setUp();

    // Use to set up again following another plugin using AJAX.
    return {
        setUp: setUp
    }

})(jQuery);
