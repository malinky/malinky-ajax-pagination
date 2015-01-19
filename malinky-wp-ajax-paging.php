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
 * Notes: Add malinky-ajax-paging-content to the main div where content will be added.
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
	     * Ajax
	     * wp_ajax_nopriv and wp_ajax followed by ajax action / the method called
	     * ------------------------------------------------------------------------ */
        add_action( 'wp_ajax_nopriv_malinky-ajax-paging-submit', array( $this, 'malinky_ajax_paging_submit' ) );
        add_action( 'wp_ajax_malinky-ajax-paging-submit', array( $this, 'malinky_ajax_paging_submit' ) );


		/* ------------------------------------------------------------------------ *
	     * Includes
	     * ------------------------------------------------------------------------ */
		include( 'malinky-ajax-paging-functions.php' );


	   	/* ------------------------------------------------------------------------ *
	     * Call Methods - LOADED LAST TO ENSURE CSS CASCADES.
	     * ------------------------------------------------------------------------ */
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_paging_styles' ), 99 );
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_paging_scripts' ), 99 );

	}


	public function malinky_ajax_paging_styles()
	{

		/**
		 * Conditional load on blog pages only and not singles.
		 */
		if ( malinky_is_blog_page( false ) ) {

			/**
			 * Ajax paging style.
			 *
			 * @link http://bxslider.com/
			 */		
			wp_register_style( 'malinky-ajax-paging', 
								MALINKY_AJAX_PAGING_PLUGIN_URL . '/css/style.css', 
								false, 
								NULL
			);
			wp_enqueue_style( 'malinky-ajax-paging' );

		}

	}


	public function malinky_ajax_paging_scripts()
	{

		/**
		 * Conditional load on blog pages only and not singles.
		 */
		if ( malinky_is_blog_page( false ) ) {

			/**
			 * Trigger ajax paging.
			 */
			wp_register_script( 'malinky-ajax-paging-main-js', 
								MALINKY_AJAX_PAGING_PLUGIN_URL . '/js/main.js', 
								array( 'jquery' ), 
								NULL, 
								true
			);

			/**
			 * Set up variables ot localize in main.js.
			 */
			global $wp_query;
			$malinky_ajax_paging['mapMaxNumPages'] 	= $wp_query->max_num_pages;
			$malinky_ajax_paging['mapNextPage'] 	= get_query_var( 'paged' ) > 1 ? get_query_var( 'paged' ) + 1 : 1 + 1;
			$malinky_ajax_paging['mapNextPageUrl'] 	= get_next_posts_page_link();
			$malinky_ajax_paging['mapQuery'] 		= $wp_query->query;
			$malinky_ajax_paging['ajaxurl'] 		= admin_url( 'admin-ajax.php' );

			wp_localize_script( 'malinky-ajax-paging-main-js', 'malinky_ajax_paging', $malinky_ajax_paging );
			wp_enqueue_script( 'malinky-ajax-paging-main-js' );

		}

	}


	public function malinky_ajax_paging_submit()
	{

		$current_page 	= isset( $_POST['mapNextPage'] ) ? $_POST['mapNextPage'] : 1;
		$current_query 	= isset( $_POST['mapQuery'] ) ? $_POST['mapQuery'] : false;

		$malinky_ajax_paging_wp_query = malinky_ajax_paging_wp_query( $current_page, $current_query );
		
		ob_start();

		while ( $malinky_ajax_paging_wp_query->have_posts() ) : $malinky_ajax_paging_wp_query->the_post();
			
			get_template_part( 'content', get_post_format() );

		endwhile; //end loop.

		$result['malinky_ajax_paging_posts'] = ob_get_clean();

		echo json_encode( $result );

        exit();

	}

}


$malinky_ajax_paging = new Malinky_Ajax_Paging();