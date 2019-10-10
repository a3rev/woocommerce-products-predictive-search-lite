<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

@set_time_limit(86400);
@ini_set("memory_limit","1000M");

add_option( 'woocommerce_search_exclude_out_stock', 'yes' );
add_option( 'woocommerce_search_cache_timeout', 1 );
add_option( 'woocommerce_search_is_debug', 'no' );

global $wc_predictive_search;
$wc_predictive_search->install_databases();

global $wc_ps_sync;
$wc_ps_sync->migrate_products_out_of_stock();
