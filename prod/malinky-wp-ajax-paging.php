<?php
/**
 * Plugin Name: Malinky Ajax Paging Plugin
 * Plugin URI: https://github.com/malinky/malinky-wp-ajax-paging
 * Description: Add ajax paging to Wordpress.
 * Version: 1.0
 * Author: Malinky
 * Author URI: https://github.com/malinky
 * License: GPL2
 *
 * Thanks:
 * @tommcfarlin for http://code.tutsplus.com/articles/how-to-integrate-the-wordpress-media-uploader-in-theme-and-plugin-options--wp-26052
 * @davidwalshblog http://davidwalsh.name/javascript-debounce-function
 * https://github.com/thomasgriffin/New-Media-Image-Uploader
 */

class Malinky_Ajax_Paging
{

	public function __construct()
	{

		//Trailing Slash
		define( 'MALINKY_AJAX_PAGING_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		//No Trailing Slash
		define( 'MALINKY_AJAX_PAGING_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ) ) );

	    //Includes
		require_once( 'malinky-ajax-paging-settings.php' );
		require_once( 'malinky-ajax-paging-functions.php' );

        //Instantiate settings object.
        $this->settngs = new Malinky_Ajax_Paging_Settings();

	    //Call Methods.
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_paging_styles' ), 99 );
	   	add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_paging_scripts' ), 99 );
	   	add_action( 'admin_enqueue_scripts', array( $this, 'malinky_ajax_paging_admin_scripts' ) );

	}


	/**
	 * Enqueue styles.
	 */
	public function malinky_ajax_paging_styles()
	{

		//Conditional load, don't include script on singles.
		if ( malinky_is_blog_page( false ) ) {

			//Ajax paging style.	
			wp_register_style( 
				'malinky-ajax-paging', 
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

		//Conditional load, don't include script on singles.
		if ( malinky_is_blog_page( false ) ) {

			//Ajax paging script.
			wp_register_script( 
				'malinky-ajax-paging-main-js', 
				MALINKY_AJAX_PAGING_PLUGIN_URL . '/js/main.js', 
				array( 'jquery' ), 
				NULL, 
				true 
			);		

			//Settings to be localized in main.js.
			global $wp_query;
			$malinky_ajax_paging_options 						= get_option( '_malinky_ajax_paging_settings' );
			$malinky_ajax_paging_options['ajax_loader']			= malinky_ajax_paging_ajax_loader( $malinky_ajax_paging_options['ajax_loader'] );
			$malinky_ajax_paging_options['max_num_pages'] 		= $wp_query->max_num_pages;
			$malinky_ajax_paging_options['next_page_number']	= get_query_var( 'paged' ) > 1 ? get_query_var( 'paged' ) + 1 : 1 + 1;
			$malinky_ajax_paging_options['next_page_url'] 		= get_next_posts_page_link();

			//Check if WooCommerce is active and then on a Woo Commerce page as different settings are used.
			$malinky_ajax_paging_options['is_woocommerce'] = in_array( 'woocommerce/woocommerce.php', get_option('active_plugins') ) && is_woocommerce();

			wp_localize_script( 'malinky-ajax-paging-main-js', 'malinky_ajax_paging_options', $malinky_ajax_paging_options );
			wp_enqueue_script( 'malinky-ajax-paging-main-js' );

		}

	}

	/**
	 * Admin enqueue scripts.
	 */
	public function malinky_ajax_paging_admin_scripts()
	{

		$malinky_ajax_paging_theme_defaults = malinky_ajax_paging_theme_defaults();

	    wp_register_script( 
	    	'malinky-ajax-paging-admin-main-js', 
	    	MALINKY_AJAX_PAGING_PLUGIN_URL . '/js/main-admin.js', 
	    	array( 'jquery' ), 
	    	NULL, 
	    	true 
	    );
	    wp_localize_script( 'malinky-ajax-paging-admin-main-js', 'malinky_ajax_paging_theme_defaults', $malinky_ajax_paging_theme_defaults );
		wp_enqueue_script( 'malinky-ajax-paging-admin-main-js' );

		//Media uploader scripts.
		wp_enqueue_media();
	    wp_register_script( 
	    	'malinky-ajax-paging-admin-media-uploader-js', 
	    	MALINKY_AJAX_PAGING_PLUGIN_URL . '/js/media-uploader.js', 
	    	array( 'jquery' ), 
	    	NULL, 
	    	true 
	    );
		wp_enqueue_script( 'malinky-ajax-paging-admin-media-uploader-js' );


		//Admin styles.
		wp_register_style( 
			'malinky-ajax-paging-admin-css', 
			MALINKY_AJAX_PAGING_PLUGIN_URL . '/css/style-admin.css', 
			false, 
			NULL 
		);
		wp_enqueue_style( 'malinky-ajax-paging-admin-css' );

	}

}

$malinky_ajax_paging = new Malinky_Ajax_Paging();