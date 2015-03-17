<?php
/**
 * Plugin Name: Malinky Ajax Paging Plugin
 * Plugin URI: https://github.com/malinky/malinky-wp-ajax-paging
 * Description: Add ajax paging.
 * Version: 1.0
 * Author: Malinky
 * Author URI: https://github.com/malinky
 * License: GPL2
 *
 * Notes:
 *
 * Need to add .malinky-ajax-paging-content to the main div where content will be added or create a setting
 * for this and then pass into main.js during localize script.
 * Also need to know the holder for the added content, for example <article> in content.php loop. This is due to the use
 * of inline-block. The content in main.js is added using .after() as .append() adds whitespace and therefore we need to know
 * <article> to the add the content after the :last-child of the other setting .malinky-ajax-paging-content.
 * Finally need the holding div for the original pagination .posts-pagination. This is removed and a new button and
 * loading div added.
 */

class Malinky_Ajax_Paging
{

	public function __construct()
	{

		//Trailing Slash
		define( 'MALINKY_AJAX_PAGING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		//No Trailing Slash
		define( 'MALINKY_AJAX_PAGING_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ) ) );

		/* ------------------------------------------------------------------------ *
	     * Includes
	     * ------------------------------------------------------------------------ */
		require_once( 'malinky-ajax-paging-settings.php' );
		require_once( 'malinky-ajax-paging-functions.php' );

        //Instantiate settings object.
        $this->settngs = new Malinky_Ajax_Paging_Settings();

	   	/* ------------------------------------------------------------------------ *
	     * Call Methods
	     * ------------------------------------------------------------------------ */
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_paging_styles' ), 99 );
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_paging_scripts' ), 99 );

	}


	/**
	 * Enqueue styles.
	 */
	public function malinky_ajax_paging_styles()
	{

		//Conditional load on blog pages only and not singles.
		if ( malinky_is_blog_page( false ) ) {

			//Ajax paging style.	
			wp_register_style( 'malinky-ajax-paging', 
								MALINKY_AJAX_PAGING_PLUGIN_URL . '/css/style.css', 
								false, 
								NULL
			);
			wp_enqueue_style( 'malinky-ajax-paging' );

		}

	}


	/**
	 * Enqueue scripts.
	 */
	public function malinky_ajax_paging_scripts()
	{

		//Conditional load on blog pages only and not singles.
		if ( malinky_is_blog_page( false ) ) {

			//Ajax paging script.
			wp_register_script( 'malinky-ajax-paging-main-js', 
								MALINKY_AJAX_PAGING_PLUGIN_URL . '/js/main.js', 
								array( 'jquery' ), 
								NULL, 
								true
			);

			//Create an array of settings to localize in main.js.
			global $wp_query;
			$malinky_ajax_paging_options 					= get_option( '_malinky_ajax_paging_settings' );
			$malinky_ajax_paging['mapPagingType']		 	= $malinky_ajax_paging_options['paging_type'];
			$malinky_ajax_paging['mapPostsWrapperClass'] 	= $malinky_ajax_paging_options['posts_wrapper'];
			$malinky_ajax_paging['mapPostClass'] 			= $malinky_ajax_paging_options['post_wrapper'];
			$malinky_ajax_paging['mapPaginationClass'] 		= $malinky_ajax_paging_options['pagination_wrapper'];
			$malinky_ajax_paging['mapMaxNumPages'] 			= $wp_query->max_num_pages;
			$malinky_ajax_paging['mapNextPage'] 			= get_query_var( 'paged' ) > 1 ? get_query_var( 'paged' ) + 1 : 1 + 1;
			$malinky_ajax_paging['mapNextPageUrl'] 			= get_next_posts_page_link();

			wp_localize_script( 'malinky-ajax-paging-main-js', 'malinky_ajax_paging', $malinky_ajax_paging );
			wp_enqueue_script( 'malinky-ajax-paging-main-js' );

		}

	}

}

$malinky_ajax_paging = new Malinky_Ajax_Paging();