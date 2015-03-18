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