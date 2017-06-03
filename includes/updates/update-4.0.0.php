<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;

// Add an index to the field comment_type to improve the response time of the query
$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_posts WHERE column_name = 'post_type'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_posts ADD INDEX post_type (post_type)" );
}

$index_exists = $wpdb->get_row( "SHOW INDEX FROM {$wpdb->prefix}ps_product_sku WHERE column_name = 'post_parent'" );
if ( is_null( $index_exists ) ) {
	$wpdb->query( "ALTER TABLE {$wpdb->prefix}ps_product_sku ADD INDEX post_parent (post_parent)" );
}