<?php

class Malinky_Ajax_Paging_Settings
{
    public function __construct()
    {
        // Add page.
        add_action( 'admin_menu', array( $this, 'malinky_ajax_paging_settings_add_page' ) );
        // Set up sections and fields.
        add_action( 'admin_init', array( $this, 'malinky_ajax_paging_settings_init' ) );
    }

    /**
     * Add an options page. Called from admin_menu action in __construct.
     *
     * @return void   
     */
    public function malinky_ajax_paging_settings_add_page()
    {
        /**
         * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
         * 
         * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
         */
        add_submenu_page(
            'options-general.php',
            'AJAX Paging Setting',
            'AJAX Paging Settings',
            'manage_options',
            'malinky-ajax-paging-settings',
            array( $this, 'malinky_ajax_paging_settings_add_page_output_callback' )
        );
    }

    /**
     * Callback which outputs the option page content.
     *
     * @return void   
     */
    public function malinky_ajax_paging_settings_add_page_output_callback()
    {
    	?>
        <div class="wrap">
            <h2>AJAX Paging Settings</h2>
            <form action="options.php" method="post">
                <?php settings_fields( 'malinky-ajax-paging-settings' ); ?>
                <div id="clone-1" class="malinky-ajax-paging-clone">
                    <?php do_settings_sections( 'malinky-ajax-paging-settings' ); ?>
                </div>
                <hr class="malinky-ajax-paging-hr" />
                <a href="#" class="malinky-ajax-paging-add-button button button-primary">Add Another</a>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register settings and set up sections and fields. Called from admin_init action.
     *
     * register_setting()
     * add_settings_section()
     * add_settings_field()
     *
     * @return void   
     */
    public function malinky_ajax_paging_settings_init()
    {

        // Populate defaults.
        $malinky_ajax_paging_theme_defaults = malinky_ajax_paging_theme_defaults();

        register_setting(
            'malinky-ajax-paging-settings', 
            '_malinky_ajax_paging_settings' 
            //'malinky_settings_validation_callback' 
        );

        $malinky_settings       = get_option( '_malinky_ajax_paging_settings' );
        $malinky_settings_total = count( $malinky_settings );

        for ( $malinky_settings_count = 0; $malinky_settings_count < $malinky_settings_total; $malinky_settings_count++ ) {

            /* ------------------------------------------------------------------------ *
             * Sections
             * ------------------------------------------------------------------------ */

            add_settings_section(
                'paging_settings_' . $malinky_settings_count,
                'Paging Settings',
                array( $this, 'malinky_ajax_paging_settings_paging_message' ),
                'malinky-ajax-paging-settings'
            );

            add_settings_section(
                'wrapper_settings_' . $malinky_settings_count,
                'Wrapper Settings',
                array( $this, 'malinky_ajax_paging_settings_wrapper_message' ),
                'malinky-ajax-paging-settings'
            );

            add_settings_section(
                'loading_settings_' . $malinky_settings_count,
                'Loading Settings',
                array( $this, 'malinky_ajax_paging_settings_loading_message' ),
                'malinky-ajax-paging-settings'
            );

            add_settings_section(
                'css_settings_' . $malinky_settings_count,
                'CSS Settings',
                array( $this, 'malinky_ajax_paging_settings_css_message' ),
                'malinky-ajax-paging-settings'
            );

            /* ------------------------------------------------------------------------ *
             * Paging Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'paging_type',
                'Paging Type',
                array( $this, 'malinky_settings_select_field' ),
                'malinky-ajax-paging-settings',
                'paging_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'paging_type',
                    'option_default'    => 'load-more',
                    'option_field_type_options' => array(
                        'infinite-scroll'   => 'Infinite Scroll',
                        'load-more'         => 'Load More',
                        'pagination'        => 'Pagination'
                    ),
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'infinite_scroll_buffer',
                'Infinite Scroll Buffer (px)',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'paging_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,    
                    'option_id'         => 'infinite_scroll_buffer',
                    'option_default'    => '20',
                    'option_count'      => $malinky_settings_count
                )
            );     

            /* ------------------------------------------------------------------------ *
             * Wrapper Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'theme_defaults',
                'Theme Defaults',
                array( $this, 'malinky_settings_select_field' ), 
                'malinky-ajax-paging-settings',
                'wrapper_settings_' . $malinky_settings_count,
                array(
                    'option_name'               => '_malinky_ajax_paging_settings',
                    'option_array'              => $malinky_settings_count,           
                    'option_id'                 => 'theme_defaults',
                    'option_default'            => 'Twenty Fifteen',
                    'option_field_type_options' => malinky_ajax_paging_theme_default_names(),
                    'option_count'              => $malinky_settings_count
                )
            );

            add_settings_field(
                'posts_wrapper',
                'Posts Wrapper',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'wrapper_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'posts_wrapper',
                    'option_default'    => $malinky_ajax_paging_theme_defaults['Twenty Fifteen']['posts_wrapper'],
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'post_wrapper',
                'Post Wrapper',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'wrapper_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'post_wrapper',
                    'option_default'    => $malinky_ajax_paging_theme_defaults['Twenty Fifteen']['post_wrapper'],
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'pagination_wrapper',
                'Pagination Wrapper',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'wrapper_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'pagination_wrapper',
                    'option_default'    => $malinky_ajax_paging_theme_defaults['Twenty Fifteen']['pagination_wrapper'],
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'next_page_selector',
                'Next Page Selector',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'wrapper_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'next_page_selector',
                    'option_default'    => $malinky_ajax_paging_theme_defaults['Twenty Fifteen']['next_page_selector'],
                    'option_count'      => $malinky_settings_count
                )
            );

            /* ------------------------------------------------------------------------ *
             * Loading Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'load_more_button_text',
                'Load More Button Text',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'loading_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'load_more_button_text',
                    'option_default'    => 'Load More Posts',
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'loading_more_posts_text',
                'Loading More Posts Text',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'loading_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,         
                    'option_id'         => 'loading_more_posts_text',
                    'option_default'    => 'Loading...' ,
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'ajax_loader',
                'AJAX Loader',
                array( $this, 'malinky_settings_ajax_loader_field' ),
                'malinky-ajax-paging-settings',
                'loading_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'ajax_loader',
                    'option_default'    => 'default',
                    'option_count'      => $malinky_settings_count
                )
            );

            /* ------------------------------------------------------------------------ *
             * CSS Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'malinky_load_more',
                'Load More Button Wrapper',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'css_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'malinky_load_more',
                    'option_default'    => 'malinky-load-more',
                    'option_count'      => $malinky_settings_count
                )
            );

            add_settings_field(
                'malinky_load_more_button',
                'Load More Button',
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-paging-settings',
                'css_settings_' . $malinky_settings_count,
                array(
                    'option_name'       => '_malinky_ajax_paging_settings',
                    'option_array'      => $malinky_settings_count,
                    'option_id'         => 'malinky_load_more_button',
                    'option_default'    => 'malinky-load-more__button',
                    'option_count'      => $malinky_settings_count
                )
            );

        }

    }

    /* ------------------------------------------------------------------------ *
     * Section Messages
     * ------------------------------------------------------------------------ */

    public function malinky_ajax_paging_settings_paging_message()
    {
    	echo 'Add an intro for the paging settings section.';
    }


    public function malinky_ajax_paging_settings_wrapper_message()
    {
    	echo 'Add an intro for the wrapper settings section.';
    }

    public function malinky_ajax_paging_settings_loading_message()
    {
        echo 'Add an intro for the loading settings section.';
    }

    public function malinky_ajax_paging_settings_css_message()
    {
        echo 'Add an intro for the css settings section.';
    }

    /**
     * Display a select field.
     *
     * @param  arr $args    option_name,
     *                      option_id,
     *                      option_default,
     *                      option_field_type_options
     * 
     * @return  void
     */
    public function malinky_settings_select_field( $args )
    {

    	$html = '';
        $html .= '<select id="' . $args['option_id'] . '_' . $args['option_count'] . '" name="' . $args['option_name'] . '[' . $args['option_array'] . '][' . $args['option_id'] . ']">';
    	$options = get_option ( $args['option_name'] );
    	foreach ( $args['option_field_type_options'] as $key => $value ) {
    		//Set selected, if value is already set
    		if ( isset( $options[ $args['option_array'] ][ $args['option_id'] ] ) && $options[ $args['option_array'] ][ $args['option_id'] ] == $key ) {
    			$selected = 'selected';
    		} else {
    			$selected = '';
    		}
    		//Set default if value is not set
    		if ( ! isset( $options[ $args['option_array'] ][ $args['option_id'] ] ) ) {
    			$selected = $args['option_default'] == $key ? 'selected' : '';
    		}
    		$html .= '<option id="' . $args['option_id'] . '_' . $args['option_count'] . '" name="' . $args['option_name'] . '[' . $args['option_array'] . '][' . $args['option_id'] . ']" value="' . esc_attr( $key ) . '"' . $selected . '/>' . esc_html( $value ) . '</option>';
    	}
    	$html .= '</select>';
    	echo $html;

    }

    /**
     * Output a text field.
     *
     * @param   arr $args   option_name,
     *                      option_id,
     *                      option_default
     * 
     * @return  void
     */
    public function malinky_settings_text_field( $args )
    {

    	$options = get_option( $args['option_name'] );
    	$html = '';
    	$html .= '<input type="text" id="' . $args['option_id'] . '_' . $args['option_count'] . '" name="' . $args['option_name'] . '[' . $args['option_array'] . '][' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_array'] ][ $args['option_id'] ] ) ? esc_attr( $options [ $args['option_array'] ][ $args['option_id'] ] ) : $args['option_default'] )  . '" placeholder="" size="30" />';	
    	echo $html;

    }

    /**
     * Output the ajax loader upload field. Uses WP Media Uploader.
     * Check if a user has uploaded an ajax loader and it is a valid image.
     * If not then use the default loader in the plugin folder.    
     *
     * @param   arr $args   option_name,
     *                      option_id,
     *                      option_default
     * 
     * @return  void
     */
    public function malinky_settings_ajax_loader_field( $args )
    {

        $ajax_loader_img = '';
        $options = get_option( $args['option_name'] );
    
        if ( isset( $options[ $args['option_array'] ][ $args['option_id'] ] ) && $options[ $args['option_array'] ][ $args['option_id'] ] != $args['option_default'] && wp_get_attachment_image( esc_attr( $options[ $args['option_array'] ][ $args['option_id'] ] ) ) != '' ) {
            $img_attr = array(
                'class' => 'malinky-ajax-paging-admin-ajax-loader',
                'alt'   => 'AJAX Loader'
            );        
            $ajax_loader_img = wp_get_attachment_image( esc_attr( $options[ $args['option_array'] ][ $args['option_id'] ] ), 'thumbnail', false, $img_attr );
        } else {
            $ajax_loader_img = '<img src="' . MALINKY_AJAX_PAGING_PLUGIN_URL . '/img/loader.gif" class="malinky-ajax-paging-admin-ajax-loader" alt="AJAX Loader" />';
            $options[ $args['option_array'] ][ $args['option_id'] ] = 'default';
        }
        
        $html = '';
        $html .= '<a href="javascript:;" id="' . $args['option_id'] . '_button' . '_' . $args['option_count'] . '">Upload AJAX Loader</a>';
        $html .= $ajax_loader_img;
        $html .= '<input type="hidden" id="' . $args['option_id'] . '_' . $args['option_count'] . '" name="' . $args['option_name'] . '[' . $args['option_array'] . '][' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_array'] ][ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_array'] ][ $args['option_id'] ] ) : $args['option_default'] )  . '" />';
        echo $html;

   }
}