<?php

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	require 'init/class-bulk-price.php';
	require 'settings/class-admin-menu.php';
	require 'product_edit/class-bulk-price-product.php';
	require 'product_edit/class-bulk-price-save.php';
	require 'data/class-bulk-value.php';
	require 'data/class-bulk-data.php';
	require 'price_display/class-price-display.php';
}