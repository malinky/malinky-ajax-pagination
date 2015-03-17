<?php

class Malinky_Ajax_Paging_Settings
{

    public function __construct()
    {

        //Add page action.
        add_action( 'admin_menu', array( $this, 'malinky_ajax_paging_settings_add_page' ) );
        //Set up sections and fields action.
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
            'ajax-paging-settings',
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
            <h2>Malinky AJAX Paging Settings</h2>
            <form action="options.php" method="post">
                <?php settings_fields( 'ajax-paging-settings' ); ?>
                <?php do_settings_sections( 'ajax-paging-settings' ); ?>
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

        register_setting(
            'ajax-paging-settings',             
            '_malinky_ajax_paging_settings'
            //'malinky_settings_validation_callback'
        );

        add_settings_section(
            'paging_settings',
            'Paging Settings',
            array( $this, 'malinky_settings_add_section_output_callback_paging_settings' ),
            'ajax-paging-settings'
        );

        add_settings_section(
            'wrapper_settings',
            'Wrapper Settings',
            array( $this, 'malinky_settings_add_section_output_callback_wrapper_settings' ),
            'ajax-paging-settings'
        );    

        add_settings_field(
            'paging_type',
            'Paging Type',
            array( $this, 'malinky_settings_select_field' ),
            'ajax-paging-settings',
            'paging_settings',
            array( 'option_name' => '_malinky_ajax_paging_settings', 'option_id' => 'paging_type', 'option_default' => 'Load More', 'option_field_type_options' => array( 'load-more' => 'Load More', 'infinite-scroll' => 'Infinite Scroll', 'pagination' => 'Pagination' ) )
        );

        add_settings_field(
            'posts_wrapper',
            'Posts Wrapper',
            array( $this, 'malinky_settings_text_field' ),
            'ajax-paging-settings',
            'wrapper_settings',
            array( 'option_name' => '_malinky_ajax_paging_settings', 'option_id' => 'posts_wrapper', 'option_default' => '.site-main' )
        );

        add_settings_field(
            'post_wrapper',
            'Post Wrapper',
            array( $this, 'malinky_settings_text_field' ),
            'ajax-paging-settings',
            'wrapper_settings',
            array( 'option_name' => '_malinky_ajax_paging_settings', 'option_id' => 'post_wrapper', 'option_default' => '.post' )
        );

        add_settings_field(
            'pagination_wrapper',
            'Pagination Wrapper',
            array( $this, 'malinky_settings_text_field' ),
            'ajax-paging-settings',
            'wrapper_settings',
            array( 'option_name' => '_malinky_ajax_paging_settings', 'option_id' => 'pagination_wrapper', 'option_default' => '.navigation' )
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


    /**
     * Display a select field.
     *
     * @param  arr $args    option_name,
     *                      option_id,
     *                      option_default,
     *                      option_field_type_options
     * @return void   
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
     * @return  void   
     */
    public function malinky_settings_text_field( $args )
    {

    	$options = get_option( $args['option_name'] );
    	$html = '';
    	$html .= '<input type="text" id="' . $args['option_id'] . '" name="' . $args['option_name'] . '[' . $args['option_id'] . ']" value="' . ( isset( $options[ $args['option_id'] ] ) ? esc_attr( $options[ $args['option_id'] ] ) : $args['option_default'] )  . '" placeholder="" />';	
    	echo $html;

    }

}