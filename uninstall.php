<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}
 
function malinky_ajax_pagination_settings_count_settings()
{
    for ( $x = 1; ; $x++ ) {
        if ( ! get_option( '_malinky_ajax_pagination_settings_' . $x ) ) return --$x;
    }
}

$total_settings = malinky_ajax_pagination_settings_count_settings();
for ( $x = 1; $x <= $total_settings; $x++ ) {
    delete_option( '_malinky_ajax_pagination_settings_' . $x );
}