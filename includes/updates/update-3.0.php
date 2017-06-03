<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

@set_time_limit(86400);
@ini_set("memory_limit","640M");

global $wpdb;

global $wc_predictive_search;
$wc_predictive_search->install_databases();

global $wc_ps_exclude_data;
$woocommerce_search_exclude_products = get_option( 'woocommerce_search_exclude_products', array() );
if ( is_array( $woocommerce_search_exclude_products ) && count( $woocommerce_search_exclude_products ) > 0 ) {
	foreach ( $woocommerce_search_exclude_products as $object_id ) {
		$wc_ps_exclude_data->insert_item( $object_id, 'product' );
	}
}

global $wc_predictive_search_admin_init;
$wc_predictive_search_admin_init->set_default_settings();

flush_rewrite_rules();

