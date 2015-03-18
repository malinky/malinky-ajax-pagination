<?php

if ( ! function_exists( 'malinky_is_blog_page' ) ) {

	/**
	 * Check if current page is the blog home page, archive or optional single.
	 * Archive includes category, tag, date, author pages, custom post types.
	 *
	 * @param bool $single Include is_single()
	 *
	 * @return bool
	 */
	function malinky_is_blog_page( $single = true )
	{

	    global $post;

	    if ( ! $single ) 
	    	return ( is_home() || is_archive() );

	    return ( is_home() || is_archive() || is_single() );

	}

}

/**
 * Check if a user has uploaded an ajax loader and it is a valid image.
 * If not then use the default loader in the plugin folder.
 * Returns an img tag to the ajax loader.
 *
 * @param int|str $ajax_loader the id of the attachment or default
 *
 * @return str
 */
function malinky_get_default_ajax_loader( $ajax_loader )
{

	if ( $ajax_loader != 'default' && wp_get_attachment_image( esc_attr( $ajax_loader ) ) != '' ) {
    	$img_attr = array(
    		'class' => 'malinky-ajax-paging-loading',
        	'alt'   => 'AJAX Loader'
    	);        
    	$ajax_loader_img = wp_get_attachment_image( esc_attr( $ajax_loader ), 'thumbnail', false, $img_attr );
    } else {
        $ajax_loader_img = '<img src="' . MALINKY_AJAX_PAGING_PLUGIN_URL . '/img/loader.gif" class="malinky-ajax-paging-loading" alt="AJAX Loader" />';
    }	

    return $ajax_loader_img;

}