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
     * Return all the settings sets options.
     *
     */
    public function malinky_get_settings_sets(){
        $all_sets = array();
        $db_sets = get_option('_malinky_ajax_pagination_settings');

        foreach((array)$db_sets as $set_key=>$set){
            $set_id = $set_key+1;
            $defaults = array(
                'set-id'    => $set_id,
                'set_name'  =>  sprintf('Paging Settings %d',$set_id),
                'set_slug'  => sanitize_title( 'paging-settings-'.$set_id)
            );

            $all_sets[$set_key] = wp_parse_args($set,$defaults);
        }
        
        return $all_sets;
    }

    /**
     * Return options for a settings set, based on its id
     * If the key parameter is defined, return that key
     *
     */
    public function malinky_get_settings_set($set_id = null, $option = null){
        
        if (!$set_id) $set_id = $this->malinky_pagination_settings_current_set_id();

        $all_sets = $this->malinky_get_settings_sets();
        $set = null;

        foreach((array)$all_sets as $loop_key=>$loop_set){
            if ( $loop_set['set-id']!=$set_id ) continue;
            $set = $loop_set;
            break;
        }

        if (!$set) return;
        
        $set = apply_filters('malinky_get_settings_set',$set,$set_id);
        
        if ($option){
            return malinky_get_array_value($option,$set);
        }
        
        return $set;

    }
    
    /**
     * Get the requested settings set id.
     *
     * @return int
     */
    public function malinky_pagination_settings_current_set_id()
    {
        $retval = 1; //avoid null values, more easy to start at 1.
        
        if ( isset($_GET['set-id']) && is_numeric($_GET['set-id']) ){
                $retval = $_GET['set-id'];
        }
        
        //form posted
        if ( isset($_POST['_malinky_ajax_pagination_settings']['set-id']) ){
            $retval = $_POST['_malinky_ajax_pagination_settings']['set-id'];
        }

        return (int)$retval;
    } 
    
    /**
     * Get the requested settings set.
     *
     * @return int
     */
    public function malinky_pagination_settings_current_set()
    {
        $id = $this->malinky_pagination_settings_current_set_id();
        return $this->malinky_get_settings_set($id);
    }

    /**
     * Delete a settings set.
     *
     * @return void
     */
    private function malinky_ajax_pagination_settings_delete( $set_id )
    {
        if ( !$set = $this->malinky_get_settings_set($set_id) ) return;
        if ( !$all_sets = $this->malinky_get_settings_sets() ) return;
        
        $set_update = array();
        
        foreach((array)$all_sets as $loop_key => $loop_set){
            if ($loop_set['set-id']!=$set_id) continue;
            $set_update = $loop_set;
            $set_update['delete'] = true;
            break;
        }
        
        if ( update_option('_malinky_ajax_pagination_settings',$set_update) ){
            //Redirect.
            $url = admin_url('options-general.php');
            $url = add_query_arg(array('page'=>'malinky-ajax-pagination-settings'),$url);
            wp_redirect($url);
        }


    }

    /**
     * Add an options page. Called from admin_menu action in __construct.
     *
     * @return void
     */
    public function malinky_ajax_pagination_settings_add_page()
    {

        /**
         * add_submenu_page( $parent_slug, $page_title, $menu_title, $capability, $menu_slug, $function );
         * 
         * @link http://codex.wordpress.org/Function_Reference/add_submenu_page
         */

        add_submenu_page(
            'options-general.php',
            __( 'Ajax Pagination Setting', 'malinky-ajax-pagination' ),
            __( 'Ajax Pagination Settings', 'malinky-ajax-pagination' ),
            'manage_options',
            'malinky-ajax-pagination-settings',
            array( $this, 'malinky_ajax_pagination_settings_add_page_output_callback' )
        );
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
            if ( is_numeric( $_GET['set-id'] ) ) {
                $this->malinky_ajax_pagination_settings_delete( $_GET['set-id'] );
            }
        }

        $set_id_requested = $this->malinky_pagination_settings_current_set_id();
        $all_sets = $this->malinky_get_settings_sets();
        
        ?>
        <div class="wrap">
            <h1><?php _e( 'Ajax Pagination Settings', 'malinky-ajax-pagination' );  ?></h1>
            <div class="malinky-ajax-pagination postbox">
                <?php 
        
                if ( $all_sets ){
                    ?>
                    <ul id="sets-tabs" class="form-table">
                        <?
                        foreach ((array)$this->malinky_get_settings_sets() as $set){

                            $url = admin_url('options-general.php');
                            $url = add_query_arg(array('page'=>'malinky-ajax-pagination-settings','set-id'=>$set['set-id']),$url);
                            $tab_classes = array();
                            if ( $set['set-id'] === $set_id_requested ){
                                $tab_classes[] = 'active';
                            }
                            printf('<li class="separator"><a href="%s" class="%s">%s</a></li>',$url,implode(' ',$tab_classes),$this->malinky_set_name($set['set-id']));
                        }
                        //Add New
                        $new_id = count( $all_sets ) + 1;
                        $url = admin_url('options-general.php');
                        $url = add_query_arg(array('page'=>'malinky-ajax-pagination-settings','set-id'=>$new_id),$url);
                        $new_set_classes = array();
                        if ( !$this->malinky_get_settings_set($set_id_requested) ) {
                            $new_set_classes[] = 'active';
                        }
                        printf('<li class="separator add-new-set"><a href="%s" class="%s">%s</a></li>',$url,implode(' ',$new_set_classes),__( 'Add New', 'malinky-ajax-pagination' ));
                    ?>
                </ul>
                <?php
                }
                ?>
                <form action="options.php" method="post">
                    <input type="hidden" name="_malinky_ajax_pagination_settings[set-id]" value="<?php echo $set_id_requested;?>" />
                    <?php 
                        settings_fields( 'malinky-ajax-pagination-settings' );
                        do_settings_sections( 'malinky-ajax-pagination-settings' );
                        if ( $this->malinky_get_settings_set($set_id_requested) ) {
                            $url = admin_url('options-general.php');
                            $url = add_query_arg(array('page'=>'malinky-ajax-pagination-settings','set-id'=>$set_id_requested,'delete'=>true,'noheader'=>true),$url);
                            printf('<a href="%s" class="%s">%s</a>',$url,'malinky-ajax-pagination-add-button button button-primary button-delete',__( 'Delete', 'malinky-ajax-pagination' ));
                        }
                        submit_button();
                    ?>
                </form>
            </div>
        </div>
        <?php
    }
    
    /**
     * Validates settings if needed
     */
    
    public function malinky_settings_set_validation_callback($input){

        $set_id = $input['set-id'];

        $all_sets = $this->malinky_get_settings_sets();
        $new_sets = array();

        //set name
        if ( isset($input['set_name'])  ){
            $input['set_name'] = trim($input['set_name']);
        }

        if( !$this->malinky_get_settings_set($set_id) ){ //is new set
            
            $all_sets[] = $input;
            
        }elseif( isset($input['delete']) ) { //delete set
            
            foreach($all_sets as $loop_key => $loop_set){

                if ($loop_set['set-id'] != $set_id) continue;
                unset($all_sets[$loop_key]);

            }
            
        }else { //update set
            
            foreach($all_sets as $loop_key => $loop_set){

                if ($loop_set['set-id'] != $set_id) continue;
                $all_sets[$loop_key] = $input;

            }
            
        }
        
        //sanitize sets
        foreach($all_sets as $loop_key => $loop_set){

            unset($loop_set['set-id']); //do not store set ID
            $new_sets[] = $loop_set;

        }

        return $new_sets;
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

        $set_id = $this->malinky_pagination_settings_current_set_id();

        register_setting(
            'malinky-ajax-pagination-settings',
            '_malinky_ajax_pagination_settings',
            array( $this, 'malinky_settings_set_validation_callback' )
        );

        /* ------------------------------------------------------------------------ *
         * Sections
         * ------------------------------------------------------------------------ */

        add_settings_section(
            'general_settings',
            __( 'General', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_ajax_pagination_set_name_message' ),
            'malinky-ajax-pagination-settings'
        );

        add_settings_section(
            'wrapper_settings',
            __( 'Wrapper Settings', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_ajax_pagination_settings_wrapper_message' ),
            'malinky-ajax-pagination-settings'
        );

        add_settings_section(
            'pagination_type_settings',
            __( 'Pagination Type Settings', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_ajax_pagination_settings_pagination_type_message' ),
            'malinky-ajax-pagination-settings'
        );

        add_settings_section(
            'loader_settings',
            __( 'Loader Settings', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_ajax_pagination_settings_loader_message' ),
            'malinky-ajax-pagination-settings'
        );

        add_settings_section(
            'load_more_button_settings',
            __( 'Load More Button Settings', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_ajax_pagination_settings_load_more_button_message' ),
            'malinky-ajax-pagination-settings'
        );

        add_settings_section(
            'callback_settings',
            __( 'Callback Settings', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_ajax_pagination_settings_callback_message' ),
            'malinky-ajax-pagination-settings'
        );

        /* ------------------------------------------------------------------------ *
         * General Settings
         * ------------------------------------------------------------------------ */

        add_settings_field(
            'set_name',
            __( 'Set Name', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_text_field' ),
            'malinky-ajax-pagination-settings',
            'general_settings',
            array(
                'option_id'         => 'set_name',
                'option_default'    => $this->malinky_set_name($set_id),
                'option_small'      => __( 'The name of this settings set.', 'malinky-ajax-pagination' )
            )
        );

        /* ------------------------------------------------------------------------ *
         * Wrapper Settings
         * ------------------------------------------------------------------------ */

        add_settings_field(
            'theme_defaults',
             __( 'Theme', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_select_field' ), 
           'malinky-ajax-pagination-settings',
            'wrapper_settings',
            array(
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
            'malinky-ajax-pagination-settings',
            'wrapper_settings',
            array(
                'option_id'         => 'posts_wrapper',
                'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['posts_wrapper'],
                'option_small'      => __( 'The selector that wraps all of the posts/products.<br /><strong>If displaying multiple paginations on the same page this must be a parent of the navigation selector.</strong>', 'malinky-ajax-pagination' )
            )
        );

        add_settings_field(
            'post_wrapper',
            __( 'Post Selector', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_text_field' ),
            'malinky-ajax-pagination-settings',
            'wrapper_settings',
            array(
                'option_id'         => 'post_wrapper',
                'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['post_wrapper'],
                'option_small'      => __( 'The selector of an individual post/product.', 'malinky-ajax-pagination' )
            )
        );

        add_settings_field(
            'pagination_wrapper',
            __( 'Navigation Selector', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_text_field' ),
            'malinky-ajax-pagination-settings',
            'wrapper_settings',
            array(
                'option_id'         => 'pagination_wrapper',
                'option_default'    => $malinky_ajax_pagination_theme_defaults['Twenty Sixteen']['pagination_wrapper'],
                'option_small'      => __( 'The selector of the post/product navigation.<br /><strong>If displaying multiple paginations on the same page this must be a child of the posts selector.</strong>', 'malinky-ajax-pagination' )
            )
        );

        add_settings_field(
            'next_page_selector',
            __( 'Next Selector', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_text_field' ),
            'malinky-ajax-pagination-settings',
            'wrapper_settings',
            array(
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
            'malinky-ajax-pagination-settings',
            'pagination_type_settings',
            array(
                'option_array'      => $set_id,
                'option_id'         => 'paging_type',
                'option_default'    => 'load-more',
                'option_field_type_options' => array(
                    'infinite-scroll'   => __('Infinite Scroll','malinky-ajax-pagination'),
                    'load-more'         => __('Load More Button','malinky-ajax-pagination'),
                    'pagination'        => __('Pagination','malinky-ajax-pagination'),
                ),
                'option_small'      => __( 'Choose a pagination type.', 'malinky-ajax-pagination' )
            )
        );

        add_settings_field(
            'infinite_scroll_buffer',
            __( 'Infinite Scroll Buffer (px)', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_text_field' ),
            'malinky-ajax-pagination-settings',
            'pagination_type_settings',
            array(
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
            'malinky-ajax-pagination-settings',
            'loader_settings',
            array(
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
            'malinky-ajax-pagination-settings',
            'load_more_button_settings',
            array(
                'option_id'         => 'load_more_button_text',
                'option_default'    => 'Load More Posts',
                'option_small'      => __( 'Change the button text.', 'malinky-ajax-pagination' )
            )
        );

        add_settings_field(
            'loading_more_posts_text',
            __( 'Loading More Posts Text', 'malinky-ajax-pagination' ),
            array( $this, 'malinky_settings_text_field' ),
           'malinky-ajax-pagination-settings',
            'load_more_button_settings',
            array(
                'option_id'         => 'loading_more_posts_text',
                'option_default'    => 'Loading...' ,
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
            'malinky-ajax-pagination-settings',
            'callback_settings',
            array(
                'option_id'         => 'callback_function',
                'option_default'    => '' ,
                'option_small'      => __( '', 'malinky-ajax-pagination' )
            )
        );
    }

    /* ------------------------------------------------------------------------ *
     * Section Messages
     * ------------------------------------------------------------------------ */
    
    public function malinky_ajax_pagination_set_name_message(){
        
    }

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
    Gets a name for the settings set or a default one
    */
    
    public function malinky_set_name($set_id){

        $name = $this->malinky_get_settings_set($set_id,'set_name');
        if ( !trim($name) ){
            $name = sprintf('Paging Settings %d',$set_id);
        }

        return $name;
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
        $field_id   = sprintf('_malinky_ajax_pagination_settings_%s',$args['option_id']);
        $field_name = sprintf('_malinky_ajax_pagination_settings[%s]',$args['option_id']);
        
        
    	$html = '';
        $html .= '<select id="' . $field_id . '" name="' . $field_name .'">';
    	$value = $this->malinky_get_settings_set( null,$args['option_id'] );

    	foreach ( $args['option_field_type_options'] as $slug => $name ) {
    		//Set selected, if value is already set
            $is_selected = ($value==$slug);
            $selected = selected( $is_selected, true, false);

    		$html .= '<option value="' . esc_attr( $slug ) . '"' . $selected . '/>' . esc_html( $name ) . '</option>';
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
        $field_id   = sprintf('_malinky_ajax_pagination_settings_%s',$args['option_id']);
        $field_name = sprintf('_malinky_ajax_pagination_settings[%s]',$args['option_id']);
    	$options = $this->malinky_get_settings_set( null );

    	$html = '';
    	$html .= '<input type="text" id="' . $field_id . '" name="' . $field_name .'" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" placeholder="" size="30" /><br /><small>' . $args['option_small'] . '</small>';
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
        
        $field_id   = sprintf('_malinky_ajax_pagination_settings_%s',$args['option_id']);
        $field_name = sprintf('_malinky_ajax_pagination_settings[%s]',$args['option_id']);
        $options = $this->malinky_get_settings_set( null,$args['option_id'] );

        $html = '';
        $html .= '<textarea id="' . $field_id . '" name="' . $field_name . '" rows="6" cols="50">' . $options . '</textarea><br /><small>' . $args['option_small'] . '</small>';
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
        
        $field_id   = sprintf('_malinky_ajax_pagination_settings_%s',$args['option_id']);
        $field_name = sprintf('_malinky_ajax_pagination_settings[%s]',$args['option_id']);
        $set_id = $this->malinky_pagination_settings_current_set_id();
        $value = $this->malinky_get_settings_set( null,$args['option_id'] );
        $link_upload_classes = $link_default_classes = array('separator');
        $ajax_loader_default_container_classes = $ajax_loader_custom_container_classes = array('ajax_loader_container');
    
        if ( $value != $args['option_default'] && wp_get_attachment_image( esc_attr( $value ) ) != '' ) {
            $img_attr = array(
                'class' => 'malinky-ajax-pagination-ajax-loader',
                'alt'   => 'AJAX Loader',
            );
            $link_upload_classes[] = 'active';
            $ajax_loader_custom_container_classes[] = 'active';
        } else {
            $options = 'default';
            $link_default_classes[] = 'active';
            $ajax_loader_default_container_classes[] = 'active';
        }
        
        $link_upload = sprintf('<span class="separator"><a href="javascript:;" id="%s" class="%s">%s</a></span>',
            $args['option_id'] . '_button',
            implode(' ',$link_upload_classes),
            __('Upload AJAX Loader','malinky-ajax-pagination')
        );
        
        $link_default = sprintf('<span class="separator"><a href="#" id="%s" class="%s">%s</a></span>',
            $args['option_id'] . '_remove',
            implode(' ',$link_default_classes),
            __('Use Original AJAX Loader','malinky-ajax-pagination')
        );
        
        $html = '';
        $html .= sprintf( '<div id="ajax_loader_default_container" class="%s">%s</div>',
            implode(' ',$ajax_loader_default_container_classes),
            malinky_ajax_pagination_get_default_loader() 
        );
        $html .= sprintf( '<div id="ajax_loader_custom_container" class="%s">%s</div>',
            implode(' ',$ajax_loader_custom_container_classes),
            sprintf('<div id="ajax_loader_custom"  class="ajax_loader"><img src="%s" /></div>',malinky_ajax_pagination_get_loader_image_url( $set_id )) 
        );
        $html .= '<p>' . $link_upload . $link_default . '</p>';
        $html .= '<input type="hidden" id="' . $field_id . '" name="' . $field_name . '" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" /><br /><small>' . $args['option_small'] . '</small>';  
        echo $html;
   }

}