<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$exclude_products = get_option('woocommerce_search_exclude_products', '');
if ($exclude_products !== false) {
	$exclude_products_array = explode(",", $exclude_products);
	if (is_array($exclude_products_array) && count($exclude_products_array) > 0) {
		$exclude_products_array_new = array();
		foreach ($exclude_products_array as $exclude_products_item) {
			if ( trim($exclude_products_item) > 0) $exclude_products_array_new[] = trim($exclude_products_item);
		}
		$exclude_products = $exclude_products_array_new;
	} else {
		$exclude_products = array();
	}
	update_option('woocommerce_search_exclude_products', (array) $exclude_products);
} else {
	update_option('woocommerce_search_exclude_products', array());
}
