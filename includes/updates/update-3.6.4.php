<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $wpdb;
$sql = "ALTER TABLE ". $wpdb->prefix . "ps_product_sku ADD post_parent BIGINT NOT NULL DEFAULT 0";
$wpdb->query($sql);