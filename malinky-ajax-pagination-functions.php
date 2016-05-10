<?php

if ( ! function_exists( 'malinky_load_css' ) ) {

    /**
     * Filter used to dequeue styles.
     */
    function malinky_load_css() {
        return apply_filters( 'malinky_load_css', MALINKY_LOAD_CSS );
    }

}

if ( ! function_exists( 'malinky_load_js' ) ) {

    /**
     * Filter used to dequeue scripts.
     */
    function malinky_load_js() {
        return apply_filters( 'malinky_load_js', MALINKY_LOAD_JS );
    }

}

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
            return ( is_home() || is_front_page() || is_archive() || is_search() );
        return ( is_home() || is_front_page() || is_archive() || is_search() || is_single() );
    }

}

if ( ! function_exists( 'malinky_ajax_pagination_ajax_loader' ) ) {

    /**
     * Check if a user has uploaded an ajax loader and it is a valid image.
     * If not then use the default loader saved in the plugin folder.
     * Returns an img tag for the ajax loader.
     *
     * @param int|str $ajax_loader the id of the attachment or the str default
     *
     * @return str
     */
    function malinky_ajax_pagination_ajax_loader( $ajax_loader )
    {
        if ( $ajax_loader != 'default' && wp_get_attachment_image( esc_attr( $ajax_loader ) ) != '' ) {
            $img_attr = array(
                'alt'   => 'AJAX Loader'
            );        
            $ajax_loader_img = wp_get_attachment_image( esc_attr( $ajax_loader ), 'thumbnail', false, $img_attr );
        } else {
            $ajax_loader_img = '<img src="' . MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/img/loader.gif" alt="AJAX Loader" />';
        }
        return $ajax_loader_img;
    }  

}

if ( ! function_exists( 'malinky_ajax_pagination_theme_defaults' ) ) {

    /**
     * Set up defaults based on popular themes.
     * Returns associative array which is then localized in main-admin.js.
     *
     * @return arr
     */
    function malinky_ajax_pagination_theme_defaults()
    {
        $theme_defaults = array(         
            'Twenty Sixteen' => array(
                'posts_wrapper'         => '.site-main',
                'post_wrapper'          => '.post',
                'pagination_wrapper'    => '.navigation',
                'next_page_selector'    => '.nav-links a.next'
            ),
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
            ),
            'WooCommerce Storefront' => array(
                'posts_wrapper'         => '.site-main',
                'post_wrapper'          => '.product',
                'pagination_wrapper'    => '.woocommerce-pagination',
                'next_page_selector'    => '.woocommerce-pagination a.next'
            )
        );
        return $theme_defaults;
    }

}

if ( ! function_exists( 'malinky_ajax_pagination_theme_default_names' ) ) {

    /**
     * Get the default themes names based on the theme type.
     *
     * @return arr
     */
    function malinky_ajax_pagination_theme_default_names()
    {
        $theme_names = malinky_ajax_pagination_theme_defaults();
        return array_combine( array_keys( $theme_names ), array_keys( $theme_names ) );
    }

}