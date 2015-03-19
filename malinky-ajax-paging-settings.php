<?php

class Malinky_Ajax_Paging_Settings
{

    public function __construct()
    {

        //Add page.
        add_action( 'admin_menu', array( $this, 'malinky_ajax_paging_settings_add_page' ) );
        //Set up sections and fields.
        add_action( 'admin_init', array( $this, 'malinky_ajax_paging_settings_init' ) );

    }


    /**
     * Add an options page. Called from admin_menu action in __construct.
     *
     * @return void   
     */
    public function malinky_ajax_paging_settings_add_page()
    {

        //add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
        //http://codex.wordpress.org/Function_Reference/add_submenu_page
        add_submenu_page(
            'options-general.php', 
            'AJAX Paging Setting', 
            'AJAX Paging Settings', 
            'manage_options', 
            'malinky-ajax-paging-settings', 
            array( $this, 'malinky_settings_add_page_output_callback' ) 
        );

    }

    /**
     * Callback which outputs the option pages content.
     *
     * @return void   
     */
    public function malinky_settings_add_page_output_callback()
    {

    	?>
        <div class="wrap">
            <h2>AJAX Paging Settings</h2>
            <form action="options.php" method="post">
                <?php settings_fields( 'malinky-ajax-paging-settings' ); ?>
                <?php do_settings_sections( 'malinky-ajax-paging-settings' ); ?>
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

        //Populate defaults for wrapper and woocommerce settings.
        $malinky_ajax_paging_theme_defaults = malinky_ajax_paging_theme_defaults();

        register_setting(
            'malinky-ajax-paging-settings', 
            '_malinky_ajax_paging_settings' 
            //'malinky_settings_validation_callback' 
        );

        /* ------------------------------------------------------------------------ *
         * Sections
         * ------------------------------------------------------------------------ */

        add_settings_section(
            'paging_settings', 
            'Paging Settings', 
            array( $this, 'malinky_settings_add_section_output_callback_paging_settings' ),  
            'malinky-ajax-paging-settings' 
        );

        add_settings_section(
            'wrapper_settings', 
            'Wrapper Settings', 
            array( $this, 'malinky_settings_add_section_output_callback_wrapper_settings' ), 
            'malinky-ajax-paging-settings' 
        );

        add_settings_section(
            'woo_wrapper_settings', 
            'WooCommerce Wrapper Settings', 
            array( $this, 'malinky_settings_add_section_output_callback_woo_wrapper_settings' ), 
            'malinky-ajax-paging-settings' 
        );        

        add_settings_section(
            'loading_settings', 
            'Loading Settings', 
            array( $this, 'malinky_settings_add_section_output_callback_loading_settings' ), 
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
            'paging_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'paging_type', 
                'option_default' => 'pagination', 
                'option_field_type_options' => array( 
                    'infinite-scroll' => 'Infinite Scroll', 
                    'load-more' => 'Load More', 
                    'pagination' => 'Pagination' 
                ) 
            )
        );

        add_settings_field(
            'infinite_scroll_buffer', 
            'Infinite Scroll Buffer (px)', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'paging_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'infinite_scroll_buffer', 
                'option_default' => '20' 
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
            'wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'theme_defaults', 
                'option_default' => 'Twenty Fifteen', 
                'option_field_type_options' => malinky_ajax_paging_theme_default_names()
            )
        );

        add_settings_field(
            'posts_wrapper', 
            'Posts Wrapper', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'posts_wrapper', 
                'option_default' => $malinky_ajax_paging_theme_defaults['theme_defaults']['Twenty Fifteen']['posts_wrapper']
            )
        );

        add_settings_field(
            'post_wrapper', 
            'Post Wrapper', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'post_wrapper', 
                'option_default' => $malinky_ajax_paging_theme_defaults['theme_defaults']['Twenty Fifteen']['post_wrapper']
            )
        );

        add_settings_field(
            'pagination_wrapper', 
            'Pagination Wrapper', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'pagination_wrapper', 
                'option_default' => $malinky_ajax_paging_theme_defaults['theme_defaults']['Twenty Fifteen']['pagination_wrapper']
            )
        );

        add_settings_field(
            'next_page_selector', 
            'Next Page Selector', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'next_page_selector', 
                'option_default' => $malinky_ajax_paging_theme_defaults['theme_defaults']['Twenty Fifteen']['next_page_selector']
            )
        );

        /* ------------------------------------------------------------------------ *
         * WooCommerce Wrapper Settings
         * ------------------------------------------------------------------------ */

        add_settings_field(
            'woo_theme_defaults', 
            'WooCommerce Theme Defaults', 
            array( $this, 'malinky_settings_select_field' ), 
            'malinky-ajax-paging-settings', 
            'woo_wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'woo_theme_defaults', 
                'option_default' => 'Storefront', 
                'option_field_type_options' => malinky_ajax_paging_theme_default_names( 'woo_theme_defaults' )
            )
        );

        add_settings_field(
            'woo_posts_wrapper', 
            'Posts Wrapper', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'woo_wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'woo_posts_wrapper', 
                'option_default' => $malinky_ajax_paging_theme_defaults['woo_theme_defaults']['Storefront']['woo_posts_wrapper']
            )
        );

        add_settings_field(
            'woo_post_wrapper', 
            'Post Wrapper', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'woo_wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'woo_post_wrapper', 
                'option_default' => $malinky_ajax_paging_theme_defaults['woo_theme_defaults']['Storefront']['woo_post_wrapper']
            )
        );

        add_settings_field(
            'woo_pagination_wrapper', 
            'Pagination Wrapper', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'woo_wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'woo_pagination_wrapper', 
                'option_default' => $malinky_ajax_paging_theme_defaults['woo_theme_defaults']['Storefront']['woo_pagination_wrapper']
            )
        );

        add_settings_field(
            'woo_next_page_selector', 
            'Next Page Selector', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'woo_wrapper_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'woo_next_page_selector', 
                'option_default' => $malinky_ajax_paging_theme_defaults['woo_theme_defaults']['Storefront']['woo_next_page_selector']
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
            'loading_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'load_more_button_text', 
                'option_default' => 'Load More Posts' 
            )
        );

        add_settings_field(
            'loading_more_posts_text', 
            'Loading More Posts Text', 
            array( $this, 'malinky_settings_text_field' ), 
            'malinky-ajax-paging-settings', 
            'loading_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'loading_more_posts_text', 
                'option_default' => 'Loading...' 
            )
        );

        add_settings_field(
            'ajax_loader', 
            'AJAX Loader', 
            array( $this, 'malinky_settings_ajax_loader_field' ), 
            'malinky-ajax-paging-settings', 
            'loading_settings', 
            array( 
                'option_name' => '_malinky_ajax_paging_settings', 
                'option_id' => 'ajax_loader', 
                'option_default' => 'default' 
            )
        );                                         

    }


    public function malinky_settings_add_section_output_callback_paging_settings()
    {

    	echo 'Add an intro for the paging settings section.';

    }


    public function malinky_settings_add_section_output_callback_wrapper_settings()
    {

    	echo 'Add an intro for the wrapper settings section.';

    }


    public function malinky_settings_add_section_output_callback_woo_wrapper_settings()
    {

        echo 'Add an intro for the WooCommerce wrapper settings section. Used on Shop pages only. Select equivalent above for Posts in a WooCommerce site.';

    }    


    public function malinky_settings_add_section_output_callback_loading_settings()
    {

        echo 'Add an intro for the loading settings section.';

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
        $html .= '<select id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']">';
    	$options = get_option ( $args['option_name'] );
    	foreach ( $args['option_field_type_options'] as $key => $value ) {
    		//Set selected, if value is already set
    		if ( isset( $args['option_id'] ) && $options[ $args['option_id'] ] == $key ) {
    			$selected = 'selected';
    		} else {
    			$selected = '';
    		}
    		//Set default if value is not set
    		if ( ! isset( $options[ $args['option_id'] ] ) ) {
    			$selected = $args['option_default'] == $key ? 'selected' : '';
    		}
    		$html .= '<option id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" value="' . esc_attr( $key ) . '"' . $selected . '/>' . esc_html( $value ) . '</option>';
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
    	$html .= '<input type="text" id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" placeholder="" size="30" />';	
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
    
        if ( $options[ $args['option_id'] ] != $args['option_default'] && wp_get_attachment_image( esc_attr( $options[ $args['option_id'] ] ) ) != '' ) {
            $img_attr = array(
                'class' => 'malinky-ajax-paging-admin-ajax-loader',
                'alt'   => 'AJAX Loader'
            );        
            $ajax_loader_img = wp_get_attachment_image( esc_attr( $options[ $args['option_id'] ] ), 'thumbnail', false, $img_attr );
        } else {
            $ajax_loader_img = '<img src="' . MALINKY_AJAX_PAGING_PLUGIN_URL . '/img/loader.gif" class="malinky-ajax-paging-admin-ajax-loader" alt="AJAX Loader" />';
            $options[ $args['option_id'] ] = 'default';
        }
        
        $html = '';
        $html .= '<a href="javascript:;" id="' . $args['option_id'] . '_button">Upload AJAX Loader</a>';
        $html .= $ajax_loader_img;
        $html .= '<input type="hidden" id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" />';
        echo $html;

   }

}