<?php

if ( ! function_exists( 'malinky_get_array_value' ) ) {
    /**
     * Get a value in a multidimensional array
     * http://stackoverflow.com/questions/1677099/how-to-use-a-string-as-an-array-index-path-to-retrieve-a-value
     * @param type $keys
     * @param type $array
     * @return type
     */
    function malinky_get_array_value($keys = null, $array){
        if (!$keys) return $array;

        $keys = (array)$keys;
        $first_key = $keys[0];
        if(count($keys) > 1) {
            if ( isset($array[$keys[0]]) ){
                return bbppu_get_array_value(array_slice($keys, 1), $array[$keys[0]]);
            }
        }elseif (isset($array[$first_key])){
            return $array[$first_key];
        }

        return false;
    }
}

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
    function malinky_ajax_pagination_ajax_loader( $set_id )
    {
        global $malinky_ajax_pagination;
        
        $set = $malinky_ajax_pagination->settings->malinky_get_settings_set($set_id);
        $ajax_loader = $set['ajax_loader'];

       $loader = malinky_ajax_pagination_get_default_loader();
        
       if ( $ajax_loader != 'default'  ) { //&& wp_get_attachment_image( esc_attr( $ajax_loader ) ) != ''

           $ajax_loader_img_url =malinky_ajax_pagination_get_loader_image_url($set_id);
           $ajax_loader_img = sprintf('<img src="%s" />',$ajax_loader_img_url);
           $loader = sprintf('<div id="ajax_loader_custom"  class="ajax_loader">%s</div>',$ajax_loader_img);
       }

       return $loader;
        
    }  
}

if ( ! function_exists( 'malinky_ajax_pagination_get_default_loader' ) ) {
    
    /**
     * Return the default loader
     * @return str
     */
    function malinky_ajax_pagination_get_default_loader()
    {
        return sprintf('<div id="ajax_loader_default" class="ajax_loader">%s</div>','<span class="spin dashicons dashicons-admin-generic"></span>');

    }
    
}

if ( ! function_exists( 'malinky_ajax_pagination_get_loader_image_url' ) ) {
    
    /**
     * Return the image URL for a set, if any
     * @return str
     */
    function malinky_ajax_pagination_get_loader_image_url( $set_id )
    {
        global $malinky_ajax_pagination;
        
        $set = $malinky_ajax_pagination->settings->malinky_get_settings_set($set_id);

        $ajax_loader = $set['ajax_loader'];
        if ( !is_numeric($ajax_loader) || !wp_get_attachment_image( esc_attr( $ajax_loader ) ) ) return;
        $src = wp_get_attachment_image_src( esc_attr( $ajax_loader ), 'thumbnail' );
        return $src[0];

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