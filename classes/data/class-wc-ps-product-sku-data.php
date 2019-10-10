<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\WCPredictiveSearch\Data;

use A3Rev\WCPredictiveSearch;

class SKU
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_product_sku = $wpdb->prefix. "ps_product_sku";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_product_sku'") != $table_ps_product_sku) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_product_sku}` (
					post_id bigint(20) NOT NULL,
					sku text NULL,
					post_parent bigint(20) NOT NULL DEFAULT 0,
					PRIMARY KEY  (post_id),
					KEY post_parent (post_parent)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Product SKU Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_product_sku';

		$wpdb->ps_product_sku = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_product_sku';
	}

	/**
	 * Insert Predictive Search Product SKU
	 */
	public function insert_item( $post_id, $sku = '', $post_parent = 0 ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_product_sku} VALUES(%d, %s, %d)", $post_id, stripslashes( $sku ), $post_parent ) );
	}

	/**
	 * Update Predictive Search Product SKU
	 */
	public function update_item( $post_id, $sku = '', $post_parent = 0 ) {
		global $wpdb;

		$value = $this->is_item_existed( $post_id );
		if ( '0' == $value ) {
			return $this->insert_item( $post_id, $sku, $post_parent );
		} else {
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ps_product_sku} SET sku = %s WHERE post_id = %d ", stripslashes( $sku ), $post_id ) );
		}
	}

	/**
	 * Get Predictive Search Product SKU
	 */
	public function get_item( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT sku FROM {$wpdb->ps_product_sku} WHERE post_id = %d LIMIT 0,1 ", $post_id ) );
	}

	/**
	 * Check Predictive Search Product SKU Existed
	 */
	public function is_item_existed( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM {$wpdb->ps_product_sku} WHERE post_id = %d LIMIT 0,1 )", $post_id ) );
	}

	/**
	 * Get Predictive Search Latest Post ID
	 */
	public function get_latest_post_id() {
		global $wpdb;

		return $wpdb->get_var( "SELECT post_id FROM {$wpdb->ps_product_sku} ORDER BY post_id DESC LIMIT 0,1" );
	}

	/**
	 * Check Latest Post ID is newest from WP database
	 */
	public function is_newest_id() {
		global $wpdb;

		$post_types = array( 'product' );

		$latest_id = $this->get_latest_post_id();
		if ( empty( $latest_id ) || is_null( $latest_id ) ) {
			$latest_id = 0;
		}

		$is_not_newest = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT EXISTS( SELECT 1 FROM {$wpdb->posts} WHERE ID > %d AND post_type IN ('". implode("','", $post_types ) ."') AND post_status = %s LIMIT 0,1 )",
				$latest_id,
				'publish'
			)
		);

		if ( '1' != $is_not_newest ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Total Items Synched
	 */
	public function get_total_items_synched() {
		global $wpdb;

		return $wpdb->get_var( "SELECT COUNT(post_id) FROM {$wpdb->ps_product_sku} " );
	}

	/**
	 * Get Total Items Need to Sync
	 */
	public function get_total_items_need_sync() {
		global $wpdb;

		//return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p INNER JOIN {$wpdb->postmeta} AS pm ON (p.ID=pm.post_id) WHERE p.post_type IN ('". implode("','", array( 'product' ) ) ."') AND p.post_status = %s AND pm.meta_key = %s AND pm.meta_value NOT LIKE '' ", 'publish', '_sku' ) );
		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(p.ID) FROM {$wpdb->posts} AS p WHERE p.post_type IN ('". implode("','", array( 'product' ) ) ."') AND p.post_status = %s ", 'publish' ) );
	}

	/**
	 * Delete Predictive Search Product SKU
	 */
	public function delete_item( $post_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_product_sku} WHERE post_id = %d ", $post_id ) );
	}

	/**
	 * Empty Predictive Search Product SKU
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_product_sku}" );
	}
}