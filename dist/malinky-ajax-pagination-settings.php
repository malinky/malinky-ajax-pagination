<?php

class Malinky_Ajax_Pagination_Settings
{
    public function __construct()
    {
        // Add page.
        add_action( 'admin_menu', array( $this, 'malinky_ajax_pagination_settings_add_page' ) );
        // Set up sections and fields.
        add_action( 'admin_init', array( $this, 'malinky_ajax_pagination_settings_init' ) );
    }

    /**
     * Return the number of settings pages saved in the database.
     *
     * @return int
     */
    public function malinky_ajax_pagination_settings_count_settings()
    {
        for ( $x = 1; ; $x++ ) {
            if ( ! get_option( '_malinky_ajax_pagination_settings_' . $x ) ) return --$x;
        }
    }

    /**
     * Get the current settings page number.
     *
     * @return int
     */
    public function malinky_ajax_pagination_settings_current_page_number()
    {
        return substr( strrchr( $_GET['page'], '-' ), 1 );
    }  

    /**
     * Get the next settings page number.
     *
     * @return int
     */
    public function malinky_ajax_pagination_settings_new_page_number()
    {
        $total_pages = $this->malinky_ajax_pagination_settings_count_settings();
        return ++$total_pages;
    }

    /**
     * Delete a settings page.
     *
     * @return void
     */
    private function malinky_ajax_pagination_settings_delete( $page_number )
    {
        $all_settings = array();
        $total_settings = $this->malinky_ajax_pagination_settings_count_settings();
        for ( $x = 1; $x <= $total_settings; $x++ ) {
            // Add remaining settings to a new array.
            // Delete each option.
            if ( $x != $page_number ) { 
                $all_settings[ $x ] = get_option( '_malinky_ajax_pagination_settings_' . $x );
            }
            delete_option( '_malinky_ajax_pagination_settings_' . $x );
        }

        // Renumber array keys.
        $all_settings = array_combine( range( 1, count( $all_settings ) ), array_values( $all_settings ) );
        
        // Save new settings.
        foreach ( $all_settings as $key => $value ) {
            add_option( '_malinky_ajax_pagination_settings_' . $key, $value );
        }

        //Redirect.
        wp_redirect('options-general.php?page=malinky-ajax-pagination-settings-1');
    }

    /**
     * Add an options page. Called from admin_menu action in __construct.
     *
     * @return void
     */
    public function malinky_ajax_pagination_settings_add_page()
    {
        for ( $x = 1; $x <= $this->malinky_ajax_pagination_settings_new_page_number(); $x++ ) {

            /**
             * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
             * 
             * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
             */
            
            // Only show first page in menu.
            $show_in_menu = $x == 1 ? 'options-general.php' : null;

            add_submenu_page(
                $show_in_menu,
                __( 'Ajax Pagination Setting', 'malinky-ajax-pagination' ),
                __( 'Ajax Pagination Settings', 'malinky-ajax-pagination' ),
                'manage_options',
                'malinky-ajax-pagination-settings-' . $x,
                array( $this, 'malinky_ajax_pagination_settings_add_page_output_callback' )
            );
        }
    }

    /**
     * Callback which outputs the option page content.
     *
     * @return void   
     */
    public function malinky_ajax_pagination_settings_add_page_output_callback()
    {   
        // Check if deleting.
        if ( isset( $_GET['delete'] ) ) {
            if ( is_numeric( $_GET['delete'] ) ) {
                $this->malinky_ajax_pagination_settings_delete( $_GET['delete'] );
            } else {
                wp_redirect('options-general.php?page=malinky-ajax-pagination-settings-1');
            }
        }

        $total_settings = $this->malinky_ajax_pagination_settings_count_settings(); ?>

        <div class="wrap">
            <h1>
                <?php _e( 'Ajax Pagination Settings', 'malinky-ajax-pagination' ); ?>
                <a href="options-general.php?page=malinky-ajax-pagination-settings-<?php echo $this->malinky_ajax_pagination_settings_new_page_number(); ?>" class="page-title-action"><?php _e( 'Add New', 'malinky-ajax-pagination' ); ?></a>
            </h1>
            <div class="malinky-ajax-pagination postbox">
                <?php for ( $x = 1; $x <= $total_settings; $x++ ) {
                    if ( $x == 1 ) { ?>
                        <div class="form-table">
                            <h3><?php _e( 'Saved Settings', 'malinky-ajax-pagination' ); ?></h3>
                    <?php } ?>
                            <a href="options-general.php?page=malinky-ajax-pagination-settings-<?php echo $x ?>" class="<?php echo $_GET['page'] == 'malinky-ajax-pagination-settings-' . $x ? ' active' : '';?>">Paging Settings <?php echo $x; ?></a>
                    <?php if ( $x < $total_settings ) { ?> | <?php }
                    if ( $x == $total_settings ) { ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                <form action="options.php" method="post">
                    <?php settings_fields( $_GET['page'] ); ?>
                    <?php do_settings_sections( $_GET['page'] ); ?>
                    <?php if ( $total_settings > 1 ) { ?>
                        <a href="options-general.php?page=malinky-ajax-pagination-settings-1&delete=<?php echo $this->malinky_ajax_pagination_settings_current_page_number(); ?>&noheader=true" class="malinky-ajax-pagination-add-button button button-primary button-delete"><?php _e( 'Delete', 'malinky-ajax-pagination' ); ?></a>
                    <?php  } ?>
                    <?php submit_button(); ?>
                </form>
            </div>
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
    public function malinky_ajax_pagination_settings_init()
    {
        // Populate defaults.
        $malinky_ajax_pagination_theme_defaults = malinky_ajax_pagination_theme_defaults();

        $total_settings = $this->malinky_ajax_pagination_settings_count_settings(); 

        for ( $x = 1; $x <= $this->malinky_ajax_pagination_settings_new_page_number(); $x++ ) {

            register_setting(
                'malinky-ajax-pagination-settings-' . $x,
                '_malinky_ajax_pagination_settings_' . $x
                //'malinky_settings_validation_callback' 
            );

            /* ------------------------------------------------------------------------ *
             * Sections
             * ------------------------------------------------------------------------ */

            add_settings_section(
                'wrapper_settings',
                __( 'Wrapper Settings', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_ajax_pagination_settings_wrapper_message' ),
                'malinky-ajax-pagination-settings-' . $x
            );

            add_settings_section(
                'pagination_type_settings',
                __( 'Pagination Type Settings', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_ajax_pagination_settings_pagination_type_message' ),
                'malinky-ajax-pagination-settings-' . $x
            );

            add_settings_section(
                'loader_settings',
                __( 'Loader Settings', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_ajax_pagination_settings_loader_message' ),
                'malinky-ajax-pagination-settings-' . $x
            );

            add_settings_section(
                'load_more_button_settings',
                __( 'Load More Button Settings', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_ajax_pagination_settings_load_more_button_message' ),
                'malinky-ajax-pagination-settings-' . $x
            );

            add_settings_section(
                'callback_settings',
                __( 'Callback Settings', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_ajax_pagination_settings_callback_message' ),
                'malinky-ajax-pagination-settings-' . $x
            );

            /* ------------------------------------------------------------------------ *
             * Wrapper Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'theme_defaults',
                 __( 'Theme Defaults', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_select_field' ), 
                'malinky-ajax-pagination-settings-' . $x,
                'wrapper_settings',
                array(
                    'option_name'               => '_malinky_ajax_pagination_settings_' . $x,        
                    'option_id'                 => 'theme_defaults',
                    'option_default'            => 'Twenty Sixteen',
                    'option_field_type_options' => malinky_ajax_pagination_theme_default_names(),
                    'option_small'              => __( 'Select from popular themes or overwrite the settings below yourself.', 'malinky-ajax-pagination' )
                )
            );

            add_settings_field(
                'posts_wrapper',
                __( 'Posts Selector', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'wrapper_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_id'         => 'posts_wrapper',
                    'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['posts_wrapper'],
                    'option_small'      => __( 'The selector that wraps all of the posts/products.<br /><strong>If displaying multiple paginations on the same page this must be a parent of the navigation selector.</strong>', 'malinky-ajax-pagination' )
                )
            );

            add_settings_field(
                'post_wrapper',
                __( 'Post Selector', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'wrapper_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_id'         => 'post_wrapper',
                    'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['post_wrapper'],
                    'option_small'      => __( 'The selector of an individual post/product.', 'malinky-ajax-pagination' )
                )
            );

            add_settings_field(
                'pagination_wrapper',
                __( 'Navigation Selector', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'wrapper_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_id'         => 'pagination_wrapper',
                    'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['pagination_wrapper'],
                    'option_small'      => __( 'The selector of the post/product navigation.<br /><strong>If displaying multiple paginations on the same page this must be a child of the posts selector.</strong>', 'malinky-ajax-pagination' )
                )
            );

            add_settings_field(
                'next_page_selector',
                __( 'Next Selector', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'wrapper_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_id'         => 'next_page_selector',
                    'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['next_page_selector'],
                    'option_small'      => __( 'The selector of the navigation next link.', 'malinky-ajax-pagination' )
                )
            );

            /* ------------------------------------------------------------------------ *
             * Pagination Type Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'paging_type',
                __( 'Paging Type', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_select_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'pagination_type_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_array'      => $x,
                    'option_id'         => 'paging_type',
                    'option_default'    => 'load-more',
                    'option_field_type_options' => array(
                        'infinite-scroll'   => 'Infinite Scroll',
                        'load-more'         => 'Load More Button',
                        'pagination'        => 'Pagination'
                    ),
                    'option_small'      => __( 'Choose a pagination type.', 'malinky-ajax-pagination' )
                )
            );

            add_settings_field(
                'infinite_scroll_buffer',
                __( 'Infinite Scroll Buffer (px)', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'pagination_type_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,  
                    'option_id'         => 'infinite_scroll_buffer',
                    'option_default'    => '20',
                    'option_small'      => __( 'The higher the buffer the earlier, during scrolling, additional posts/products will be loaded.<br /><em>Only used when Infinite Scroll is selected as the paging type.</em>', 'malinky-ajax-pagination' )
                )
            );

            /* ------------------------------------------------------------------------ *
             * Loader Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'ajax_loader',
                __( 'AJAX Loader', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_ajax_loader_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'loader_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_id'         => 'ajax_loader',
                    'option_default'    => 'default',
                    'option_small'      => ''
                )
            );

            /* ------------------------------------------------------------------------ *
             * Load More Button Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'load_more_button_text',
                __( 'Load More Button Text', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'load_more_button_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,
                    'option_id'         => 'load_more_button_text',
                    'option_default'    => __( 'Load More Posts', 'malinky-ajax-pagination' ),
                    'option_small'      => __( 'Change the button text.', 'malinky-ajax-pagination' )
                )
            );

            add_settings_field(
                'loading_more_posts_text',
                __( 'Loading More Posts Text', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_text_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'load_more_button_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,    
                    'option_id'         => 'loading_more_posts_text',
                    'option_default'    => __( 'Loading...', 'malinky-ajax-pagination' ),
                    'option_small'      => __( 'Change the text that is displayed on the button while new posts/products are being loaded.', 'malinky-ajax-pagination' )
                )
            );

            /* ------------------------------------------------------------------------ *
             * Callback Settings
             * ------------------------------------------------------------------------ */

            add_settings_field(
                'callback_function',
                __( 'Callback', 'malinky-ajax-pagination' ),
                array( $this, 'malinky_settings_textarea_field' ),
                'malinky-ajax-pagination-settings-' . $x,
                'callback_settings',
                array(
                    'option_name'       => '_malinky_ajax_pagination_settings_' . $x,    
                    'option_id'         => 'callback_function',
                    'option_default'    => '' ,
                    'option_small'      => __( '', 'malinky-ajax-pagination' )
                )
            );

        }
    }

    /* ------------------------------------------------------------------------ *
     * Section Messages
     * ------------------------------------------------------------------------ */

    public function malinky_ajax_pagination_settings_wrapper_message()
    {
        _e( '<p>These options allow you to set the selectors that contain your posts/products and pagination.<br />If you use a popular theme it may be listed in the Theme Defaults, if not just overwrite the settings.<br /><em>Include a leading . before the selector names.</em></p>', 'malinky-ajax-pagination' );
    }

    public function malinky_ajax_pagination_settings_pagination_type_message()
    {
    	_e( '<p>These options control the type of ajax pagination used.<br />Infinite Scroll automatically loads new posts/products as the user scrolls to the bottom of the screen.<br />Load More Button displays a single button at the bottom of the posts/products that when clicked loads new posts/products via ajax.<br />Pagination displays the themes standard pagination but new posts/products are now loaded via ajax.</p>', 'malinky-ajax-pagination' );
    }

    public function malinky_ajax_pagination_settings_loader_message()
    {
        _e( '<p>This option allows you to upload a new preloader .gif.</p>', 'malinky-ajax-pagination' );
    }

    public function malinky_ajax_pagination_settings_load_more_button_message()
    {
        _e( '<p>These options allow you to override the button text if Load More Button is selected as the paging type.</p>', 'malinky-ajax-pagination' );
    }

    public function malinky_ajax_pagination_settings_callback_message()
    {
        _e( '<p>Code that is called after each new set of posts are loaded.<br />Receives two parameters: loadedPosts (An array of the new posts) and url (The url that was loaded).</p>', 'malinky-ajax-pagination' );
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
    		if ( isset( $options[ $args['option_id'] ] ) && $options[ $args['option_id'] ] == $key ) {
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
    	$html .= '</select><br /><small>' . $args['option_small'] . '</small>';
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
    	$html .= '<input type="text" id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" placeholder="" size="30" /><br /><small>' . $args['option_small'] . '</small>';
    	echo $html;
    }

    /**
     * Output a textarea field.
     *
     * @param   arr $args   option_name,
     *                      option_id,
     *                      option_default
     * 
     * @return  void
     */
    public function malinky_settings_textarea_field( $args )
    {
        $options = get_option( $args['option_name'] );
        $html = '';
        $html .= '<textarea id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" rows="6" cols="50">' . (isset($options[ $args['option_id'] ]) ? $options[ $args['option_id'] ] : '') . '</textarea><br /><small>' . $args['option_small'] . '</small>';
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
    
        if ( isset( $options[ $args['option_id'] ] ) && $options[ $args['option_id'] ] != $args['option_default'] && wp_get_attachment_image( esc_attr( $options[ $args['option_id'] ] ) ) != '' ) {
            $img_attr = array(
                'class' => 'malinky-ajax-pagination-ajax-loader',
                'alt'   => 'AJAX Loader',
            );        
            $ajax_loader_img = wp_get_attachment_image( esc_attr( $options[ $args['option_id'] ] ), 'thumbnail', false, $img_attr );
        } else {
            $ajax_loader_img = '<img src="' . MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/img/loader.gif" class="malinky-ajax-pagination-ajax-loader" alt="AJAX Loader" />';
            $options[ $args['option_id'] ] = 'default';
        }
        
        $html = '';
        $html .= $ajax_loader_img;
        $html .= '<p><a href="javascript:;" id="' . $args['option_id'] . '_button' . '">Upload AJAX Loader</a> | ';
        $html .= '<a href="' . MALINKY_AJAX_PAGINATION_PLUGIN_URL . '/img/loader.gif" id="' . $args['option_id'] . '_remove' . '">Use Original AJAX Loader</a></p>';
        $html .= '<input type="hidden" id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" /><br /><small>' . $args['option_small'] . '</small>';  
        echo $html;
   }
}