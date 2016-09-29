<?php
/**
 * Plugin Name: Ajax Pagination and Infinite Scroll
 * Plugin URI: https://github.com/malinky/malinky-ajax-pagination
 * Description: Choose from infinite scroll, load more button and pagination to load paged content with Ajax on your posts, pages, custom post types and WooCommerce. Multiple pagination settings can be created for different post types and templates.
 * Version: 1.3.0
 * Author: Malinky
 * Author URI: https://github.com/malinky
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: malinky-ajax-pagination
 * Domain Path: /languages
 */

class Malinky_Ajax_Pagination
{
	public function __construct()
	{
		// Trailing Slash.
		define( 'MALINKY_AJAX_PAGINATION_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		// No Trailing Slash.
		define( 'MALINKY_AJAX_PAGINATION_PLUGIN_URL', plugins_url( basename( plugin_dir_path( __FILE__ ) ) ) );
        // Database version
        define( 'MALINKY_AJAX_PAGINATION_DB', 131);

		// Constant for enqueuing css.
		if ( ! defined( 'MALINKY_LOAD_CSS' ) ) {
			define( 'MALINKY_LOAD_CSS', true );
		}

		// Constand for enqueuing js.
        if ( ! defined( 'MALINKY_LOAD_JS' ) ) {
			define( 'MALINKY_LOAD_JS', true );
		}

	    // Includes.
		require_once( 'malinky-ajax-pagination-settings.php' );
		require_once( 'malinky-ajax-pagination-functions.php' );

        // Instantiate settings object.
        $this->settings = new Malinky_Ajax_Pagination_Settings();
        
        // Install or upgrade
        add_action( 'init' , array($this, 'malinky_ajax_pagination_upgrade'));//install and upgrade

	    // Enqueue styles and scripts.
   		add_action( 'wp_enqueue_scripts', array( $this, 'malinky_ajax_pagination_enqueue_scripts' ), 99 );
	   	add_action( 'admin_enqueue_scripts', array( $this, 'malinky_ajax_pagination_admin_scripts' ) );
	   	add_action( 'plugins_loaded', array( $this, 'malinky_ajax_pagination_load_textdomain' ) );
	}
    
	/**
	 * Install or upgrade the plugin
	 */
    
    public function malinky_ajax_pagination_upgrade(){
        global $wpdb;
        $current_version = get_option('_malinky_ajax_pagination_db');

        if(!$current_version){ //install
            
            //we were not storing the DB version before v1.3.0
            //upgrade data from <1.3.0 if any.
            if ( $rows = $wpdb->get_results($wpdb->prepare("SELECT * FROM $wpdb->options WHERE option_name LIKE '%s'",'_malinky_ajax_pagination_settings_%'), ARRAY_A) ){
                
                $new_option = array();
                
                foreach($rows as $set_id=>$old_settings_set){
                    $new_set = $old_settings_set;
                    $new_set['set_name'] = sprintf('Paging Settings %d',$set_id);
                    $new_set['set_slug'] = sanitize_title( $new_set['set_name'], 'paging-settings-'.$set_id );
                    $new_option[] = $new_set;
                }
                update_option('_malinky_ajax_pagination_settings',$new_option);
                
                //delete old data
                $wpdb->query($wpdb->prepare("DELETE FROM $wpdb->options WHERE option_name LIKE '%s'",'_malinky_ajax_pagination_settings_%'));
                
            }
            
        }else{ //upgrade

        }

        //upgrade DB version
        update_option('_malinky_ajax_pagination_db', MALINKY_AJAX_PAGINATION_DB );//upgrade DB version
    }

	/**
	 * Conditionally enqueue styles.
	 */
	function malinky_ajax_pagination_enqueue_scripts()
	{
		if ( malinky_load_css() ) {
			$this->malinky_ajax_pagination_styles();
		}
		if ( malinky_load_js() ) {
			$this->malinky_ajax_pagination_scripts();
		}		
	}

	/**
	 * Load plugin textdomain.
	 */
	public function malinky_ajax_pagination_load_textdomain()
	{
  		load_plugin_textdomain( 'malinky-ajax-pagination', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' ); 
	}

	/**
	 * Enqueue styles.
	 */
	public function malinky_ajax_pagination_styles()
	{
		wp_register_style(
			'malinky-ajax-pagination',
			MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/css/style.css',
			false,
			NULL
		);
		wp_enqueue_style( 'malinky-ajax-pagination' );
	}

	/**
	 * Enqueue scripts.
	 */
	public function malinky_ajax_pagination_scripts()
	{
		wp_register_script(
			'malinky-ajax-pagination-main-js',
			MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/js/main.js',
			array( 'jquery' ),
			NULL,
			true
		);

		// If no settings have been saved yet.
        if ( !$all_sets = $this->settings->malinky_get_settings_sets() ) return;

		// Set ajax loader images.
		foreach ( $all_sets as $loop_key => $loop_set ) {
            $set_id = $loop_set['set-id'];
			$all_sets[$loop_key]['ajax_loader'] = malinky_ajax_pagination_ajax_loader( $set_id );	
		}
        
		wp_localize_script( 'malinky-ajax-pagination-main-js', 'malinkySettings', $all_sets );
		wp_enqueue_script( 'malinky-ajax-pagination-main-js' );
	}

	/**
	 * Admin enqueue styles and scripts.
	 */
	public function malinky_ajax_pagination_admin_scripts()
	{
		wp_register_style(
			'malinky-ajax-pagination-admin-css',
			MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/css/style-admin.css',
			false,
			NULL
		);
		wp_enqueue_style( 'malinky-ajax-pagination-admin-css' );

		// Get theme defaults.
		$malinky_ajax_pagination_theme_defaults = malinky_ajax_pagination_theme_defaults();

	    wp_register_script(
	    	'malinky-ajax-pagination-admin-main-js',
	    	MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/js/main-admin.js',
	    	array( 'jquery' ),
	    	NULL,
	    	true
	    );
	    wp_localize_script( 'malinky-ajax-pagination-admin-main-js', 'malinkyAjaxPagingThemeDefaults', $malinky_ajax_pagination_theme_defaults );
		wp_enqueue_script( 'malinky-ajax-pagination-admin-main-js' );


		wp_enqueue_media();
	    wp_register_script(
	    	'malinky-ajax-pagination-admin-media-uploader-js',
	    	MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/js/media-uploader.js',
	    	array( 'jquery' ),
	    	NULL,
	    	true
	    );
		wp_enqueue_script( 'malinky-ajax-pagination-admin-media-uploader-js' );
	}
}

$malinky_ajax_pagination = new Malinky_Ajax_Pagination();