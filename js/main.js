/* ------------------------------------------------------------------------ *
 * jQuery
 * ------------------------------------------------------------------------ */

jQuery(document).ready(function($){

    var mapMaxNumPages  = parseInt(malinky_ajax_paging.mapMaxNumPages),
        mapNextPage     = parseInt(malinky_ajax_paging.mapNextPage),
        mapNextPageUrl  = malinky_ajax_paging.mapNextPageUrl,
        mapQuery        = malinky_ajax_paging.mapQuery;


    /**
     * Initialize.
     */
    mapPagination();


    /**
     * Remove the existing and add new pagination.
     */
    function mapPagination()
    {

        $('.posts-pagination').empty();
        $('.posts-pagination').append('<a href="' + mapNextPageUrl + '" class="ajax-paging-load-more-button button full-width">More Projects</a>');
        $('.posts-pagination').before('<div class="malinky-ajax-paging-loading"></div>');
        
    }


    /**
     * Load and append posts.
     */
    function mapPosts()
    {

        var data = {
            action:        'malinky-ajax-paging-submit',
            mapNextPage:   mapNextPage,
            mapQuery:      mapQuery
        };

        /**
         * Load the content of the next page.
         * Find the .archive-content divs only found in content.php.
         * Add after the last article of .malinky-ajax-paging-content.
         * .after() is used as it doesn't add whitespace where using .append() breaks the layout due to
         * the use of display: inline-block.
         */
        $.ajax({
                type:       'GET',
                url:        malinky_ajax_paging.ajaxurl,
                data:       data,
                success:    function(response) {

                                var result = $.parseJSON(response);

                                /**
                                 * Debug result, also set in malinky_ajax_paging_submit() and malinky_ajax_paging_wp_query().
                                 * console.log(result);
                                 */ 
                                
                                $('.malinky-ajax-paging-content article:last-child').after(result.malinky_ajax_paging_posts);

                                /**
                                 * Remove loading div and clear timer.s
                                 */
                                mapLoaded();
                                clearTimeout(mapLoadingTimer);

                                /**
                                 * Increment page number.
                                 */
                                mapNextPage++;

                                /**
                                 * Check the new next page number is not greater than the max pages.
                                 */
                                if (mapNextPage > mapMaxNumPages) {
                                    $('.posts-pagination').remove();
                                    return false;
                                }

                                /**
                                 * Create new page url for button link.
                                 */
                                mapNextPageUrl = mapNextPageUrl.replace(/\/page\/[0-9]?/, '/page/'+ mapNextPage);

                            }
        });

    }


    /**
     * Add loading text to button and loader.gif
     */
    function mapLoading()
    {

        $('.ajax-paging-load-more-button').text('Loading Projects');
        $('.malinky-ajax-paging-loading').show();

    }


    /**
     * Remove loading text to button and loader.gif
     */
    function mapLoaded()
    {

        $('.ajax-paging-load-more-button').text('More Projects');
        $('.malinky-ajax-paging-loading').hide();

    }    

    /**
     * Use .on as the pagination is added after page load and we need to use delegated event.
     */
    $('.posts-pagination').on( 'click', '.ajax-paging-load-more-button', function(event) {

        /**
         * Delay loading text and div.
         */
        mapLoadingTimer = setTimeout(mapLoading, 750);

        /**
         * Debug timer. Remove mapPosts call and use setTimeout instead.
         * setTimeout(mapPosts, 3000);
         */
        mapPosts();

        event.preventDefault();

    });

});