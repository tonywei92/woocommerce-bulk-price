<?php
/*
Plugin Name:  Bulk Price for WooCommerce
Plugin URI:   https://developer.wordpress.org/plugins/the-basics/
Description:  A bulk price plugin for WooCommerce
Version:      1.0
Author:       Tony Song
Author URI:   https://www.tonysong.io
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  wporg
Domain Path:  /languages
*/


if ( ! function_exists('write_log')) {
    function write_log ( $log )  {
        if ( is_array( $log ) || is_object( $log ) ) {
            error_log( print_r( $log, true ) );
        } else {
            error_log( $log );
        }
    }
}


add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'my_plugin_action_links' );

function my_plugin_action_links( $links ) {
	$links[] = '<a href="'. esc_url( get_admin_url(null, 'admin.php?page=woocommerce-bulkprice') ) .'">Settings</a>';
	if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		$links[] = '<span style="color:red">Woocommerce is not activated</span>';
	}
	return $links;
}

require 'classes/bulk_price/bootstrap.php';