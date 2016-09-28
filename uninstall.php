<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

delete_option( '_malinky_ajax_pagination_settings' );
delete_option('_malinky_ajax_pagination_db' );