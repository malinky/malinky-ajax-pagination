<?php

if ( ! function_exists( 'malinky_is_blog_page' ) ) {

	/**
	 * Check if current page is the blog home page, archive or single.
	 * Archive includes category, tag, date, author pages.
	 * The function excludes CPT to just use native blog/posts.
	 *
	 * @param bool $single Whether to include is_single()
	 *
	 * @return bool
	 */
	function malinky_is_blog_page( $single = true )
	{

	    global $post;

	    $post_type = get_post_type($post);

	    if ( ! $single ) 
	    	return ( ( is_home() || is_archive() ) && ( $post_type == 'post' ) );

	    return ( ( is_home() || is_archive() || is_single() ) && ( $post_type == 'post' ) );

	}

}


if ( ! function_exists( 'malinky_ajax_paging_wp_query' ) ) {

	/**
	 *
	 */
	function malinky_ajax_paging_wp_query( $current_page, $current_query )
	{

		$posts_per_page = get_option( 'posts_per_page' );

		$offset = ( max( 1, $current_page ) - 1 ) * $posts_per_page;

		$args = array(
			'posts_per_page'	=> $posts_per_page,
			'offset'            => $offset,
		);
		
		/*
		 * If we have a current query it will be in the format.
		 * [category_name] = football-projects or ['tag'] = drainage.
		 */
		if ($current_query) {
			$args[ key( $current_query ) ] = $current_query[ key( $current_query ) ];
		}

		return new WP_Query($args);

	}

}