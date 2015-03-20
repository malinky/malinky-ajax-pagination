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
 * Returns an img tag for the ajax loader.
 *
 * @param int|str $ajax_loader the id of the attachment or the str default
 *
 * @return str
 */
function malinky_ajax_paging_ajax_loader( $ajax_loader )
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


/**
 * Set up defaults based on popular theme names split into blog and WooCommerce.
 * Returns associative array which is then localized into main-admin.js.
 *
 * @return arr
 */
function malinky_ajax_paging_theme_defaults()
{

	$theme_defaults = array(
        'theme_defaults' => array(
            'Twenty Fifteen' => array(
                'posts_wrapper'         => '.site-main', 
                'post_wrapper'          => '.post', 
                'pagination_wrapper'    => '.nav-links', 
                'next_page_selector'    => '.nav-links a.next'
            ), 
            'Twenty Fourteen' => array(
                'posts_wrapper'         => '.site-content', 
                'post_wrapper'          => '.post', 
                'pagination_wrapper'    => '.pagination', 
                'next_page_selector'    => '.pagination a.next'
            ), 
            'Twenty Thirteen' => array(
                'posts_wrapper'         => '.site-content', 
                'post_wrapper'          => '.post', 
                'pagination_wrapper'    => '.nav-links', 
                'next_page_selector'    => '.nav-links .nav-previous a'
            ), 
            'Twenty Twelve' => array(
                'posts_wrapper'         => '.site-content', 
                'post_wrapper'          => '.post', 
                'pagination_wrapper'    => '#nav-below', 
                'next_page_selector'    => '#nav-below .nav-previous a'
            ), 
            'Storefront' => array(
                'posts_wrapper'         => '.site-main', 
                'post_wrapper'          => '.post', 
                'pagination_wrapper'    => '.storefront-pagination', 
                'next_page_selector'    => '.storefront-pagination a.next'
            )
        ),
       'woo_theme_defaults' => array(
            'Storefront' => array(
                'woo_posts_wrapper'         => '.site-main', 
                'woo_post_wrapper'          => '.product', 
                'woo_pagination_wrapper'    => '.woocommerce-pagination', 
                'woo_next_page_selector'    => '.woocommerce-pagination a.next'
            )
        )
    );

    return $theme_defaults;

}


/**
 * Get the default themes names based on the theme type.
 *
 * @param $theme_type Either 'Blog' or 'WooCommerce'.
 *
 * @return arr
 */
function malinky_ajax_paging_theme_default_names( $theme_type = 'theme_defaults' ) {

    $theme_names = '';
    $theme_defaults = malinky_ajax_paging_theme_defaults();
    $theme_defaults = array_keys( $theme_defaults[$theme_type] );
    $theme_defaults = array_combine( $theme_defaults, $theme_defaults );
    return $theme_defaults;

}