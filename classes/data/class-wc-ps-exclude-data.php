<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

class WC_PS_Exclude_Data
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_exclude = $wpdb->prefix. "ps_exclude";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_exclude'") != $table_ps_exclude) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_exclude}` (
					object_id bigint(20) NOT NULL,
					object_type VARCHAR(20) NOT NULL DEFAULT 'post',
					PRIMARY KEY (object_id,object_type)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Exclude Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_exclude';

		$wpdb->ps_exclude = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_exclude';
	}

	/**
	 * Insert Predictive Search Exclude
	 */
	public function insert_item( $object_id, $object_type = 'post' ) {
		global $wpdb;

		$value = $this->get_item( $object_id, $object_type );
		if ( NULL == $value ) {
			return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_exclude} VALUES(%d, %s)", $object_id, stripslashes( $object_type ) ) );
		} else {
			return false;
		}
	}

	/**
	 * Get Predictive Search Item Exclude
	 */
	public function get_item( $object_id, $object_type = 'post' ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT object_id FROM {$wpdb->ps_exclude} WHERE object_id = %d AND object_type = %s LIMIT 0,1 ", $object_id, stripslashes( $object_type ) ) );
	}

	/**
	 * Get Predictive Search Items Exclude
	 */
	public function get_items( $object_type = 'post' ) {
		global $wpdb;
		return $wpdb->get_results( $wpdb->prepare( "SELECT object_id FROM {$wpdb->ps_exclude} WHERE object_type = %s ", stripslashes( $object_type ) ) );
	}

	/**
	 * Get Predictive Search Array Items Exclude
	 */
	public function get_array_items( $object_type = 'post' ) {
		global $wpdb;
		$items = $wpdb->get_col( $wpdb->prepare( "SELECT object_id FROM {$wpdb->ps_exclude} WHERE object_type = %s ", stripslashes( $object_type ) ) );
		if ( ! empty( $items ) ) {
			$items = array_diff( $items, array( 0, '0' ) );
		}

		return $items;
	}

	/**
	 * Delete Predictive Search Item Exclude
	 */
	public function delete_item( $object_id, $object_type = 'post' ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_exclude} WHERE object_id = %d AND object_type = %s ", $object_id, stripslashes( $object_type ) ) );
	}

	/**
	 * Delete Predictive Search Items Exclude
	 */
	public function delete_items( $object_type = 'post' ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_exclude} WHERE object_type = %s ", stripslashes( $object_type ) ) );
	}

	/**
	 * Empty Predictive Search Posts
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_exclude}" );
	}

}

global $wc_ps_exclude_data;
$wc_ps_exclude_data = new WC_PS_Exclude_Data();
?>