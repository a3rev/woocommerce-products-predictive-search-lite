<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\WCPredictiveSearch\Data;

use A3Rev\WCPredictiveSearch;

class Posts
{
	public function install_database() {
		global $wpdb;
		$collate = '';
		if ( $wpdb->has_cap( 'collation' ) ) {
			if( ! empty($wpdb->charset ) ) $collate .= "DEFAULT CHARACTER SET $wpdb->charset";
			if( ! empty($wpdb->collate ) ) $collate .= " COLLATE $wpdb->collate";
		}

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$table_ps_posts = $wpdb->prefix. "ps_posts";

		if ($wpdb->get_var("SHOW TABLES LIKE '$table_ps_posts'") != $table_ps_posts) {
			$sql = "CREATE TABLE IF NOT EXISTS `{$table_ps_posts}` (
					post_id bigint(20) NOT NULL,
					post_title mediumtext NOT NULL,
					post_type VARCHAR(20) NOT NULL DEFAULT 'post',
					PRIMARY KEY  (post_id),
					KEY post_type (post_type)
				) $collate; ";

			$wpdb->query($sql);
		}

	}

	/**
	 * Predictive Search Post Table - set table name
	 *
	 * @return void
	 */
	public function set_table_wpdbfix() {
		global $wpdb;
		$meta_name = 'ps_posts';

		$wpdb->ps_posts = $wpdb->prefix . $meta_name;

		$wpdb->tables[] = 'ps_posts';
	}

	/**
	 * Predictive Search Post Table - return sql
	 *
	 * @return void
	 */
	public function get_sql( $search_keyword = '', $search_keyword_nospecial = '', $post_type = 'product', $number_row, $start = 0, $check_existed = false ) {
		if ( '' == $search_keyword && '' == $search_keyword_nospecial ) {
			return false;
		}

		global $wpdb;
		global $wc_ps_exclude_data;

		$sql     = array();
		$join    = array();
		$where   = array();
		$groupby = array();
		$orderby = array();

		$where[] = " 1=1 ";

		$items_excluded = apply_filters( 'wc_ps_items_excluded', $wc_ps_exclude_data->get_array_items( $post_type ), $post_type );

		if ( 'page' == $post_type ) {
			global $woocommerce_search_page_id;
			$items_excluded = array_merge( array( (int) $woocommerce_search_page_id ), $items_excluded );
		}

		$woocommerce_search_exclude_out_stock = get_option('woocommerce_search_exclude_out_stock');
		if ( 'yes' == $woocommerce_search_exclude_out_stock && in_array( $post_type, array( 'product', 'product_variation' ) ) ) {
			global $wc_ps_postmeta_data;
			$exclude_outofstock_sql = $wc_ps_postmeta_data->exclude_outofstock_sql();
			$join                   = array_merge( $join, $exclude_outofstock_sql['join'] );
			$where                  = array_merge( $where, $exclude_outofstock_sql['where'] );
		}

		$id_excluded    = '';
		if ( ! empty( $items_excluded ) ) {
			$id_excluded = implode( ',', $items_excluded );
		}

		$sql['select']   = array();
		if ( $check_existed ) {
			$sql['select'][] = " 1 ";
		} else {
			$sql['select'][] = " pp.* ";
		}

		$sql['from']   = array();
		$sql['from'][] = " {$wpdb->ps_posts} AS pp ";

		$sql['join']   = $join;

		$where[] = $wpdb->prepare( " AND pp.post_type = %s", $post_type );

		if ( '' != trim( $id_excluded ) ) {
			$where[] = " AND pp.post_id NOT IN ({$id_excluded}) ";
		}

		$where_title = ' ( ';
		$where_title .= WCPredictiveSearch\Functions::remove_special_characters_in_mysql( 'pp.post_title', $search_keyword );
		if ( '' != $search_keyword_nospecial ) {
			$where_title .= " OR ". WCPredictiveSearch\Functions::remove_special_characters_in_mysql( 'pp.post_title', $search_keyword_nospecial );
		}
		$search_keyword_no_s_letter = WCPredictiveSearch\Functions::remove_s_letter_at_end_word( $search_keyword );
		if ( $search_keyword_no_s_letter != false ) {
			$where_title .= " OR ". WCPredictiveSearch\Functions::remove_special_characters_in_mysql( 'pp.post_title', $search_keyword_no_s_letter );
		}
		$where_title .= ' ) ';

		$where['search']   = array();
		$where['search'][] = ' ( ' . $where_title . ' ) ';

		$sql['where']      = $where;

		$sql['groupby']    = array();
		$sql['groupby'][]  = ' pp.post_id ';

		$sql['orderby']    = array();
		if ( $check_existed ) {
			$sql['limit']      = " 0 , 1 ";
		} else {
			global $predictive_search_mode;

			$multi_keywords = explode( ' ', trim( $search_keyword ) );
			if ( 'broad' != $predictive_search_mode ) {
				$sql['orderby'][]  = $wpdb->prepare( " pp.post_title NOT LIKE '%s' ASC, pp.post_title ASC ", $search_keyword.'%' );
				foreach ( $multi_keywords as $single_keyword ) {
					$sql['orderby'][]  = $wpdb->prepare( " pp.post_title NOT LIKE '%s' ASC, pp.post_title ASC ", $single_keyword.'%' );
				}
			} else {
				$sql['orderby'][]  = $wpdb->prepare( " pp.post_title NOT LIKE '%s' ASC, pp.post_title NOT LIKE '%s' ASC, pp.post_title ASC ", $search_keyword.'%', '% '.$search_keyword.'%' );
				foreach ( $multi_keywords as $single_keyword ) {
					$sql['orderby'][]  = $wpdb->prepare( " pp.post_title NOT LIKE '%s' ASC, pp.post_title NOT LIKE '%s' ASC, pp.post_title ASC ", $single_keyword.'%', '% '.$single_keyword.'%' );
				}
			}

			$sql['limit']      = " {$start} , {$number_row} ";
		}

		return $sql;
	}

	/**
	 * Insert Predictive Search Post
	 */
	public function insert_item( $post_id, $post_title = '', $post_type = 'post' ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->ps_posts} VALUES(%d, %s, %s)", $post_id, stripslashes( $post_title ), stripslashes( $post_type ) ) );
	}

	/**
	 * Update Predictive Search Post
	 */
	public function update_item( $post_id, $post_title = '', $post_type = 'post' ) {
		global $wpdb;

		$value = $this->is_item_existed( $post_id );
		if ( '0' == $value ) {
			return $this->insert_item( $post_id, $post_title, $post_type );
		} else {
			return $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->ps_posts} SET post_title = %s, post_type = %s WHERE post_id = %d ", stripslashes( $post_title ), stripslashes( $post_type ), $post_id ) );
		}
	}

	/**
	 * Get Predictive Search Post
	 */
	public function get_item( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT post_title FROM {$wpdb->ps_posts} WHERE post_id = %d LIMIT 0,1", $post_id ) );
	}

	/**
	 * Check Predictive Search Post Existed
	 */
	public function is_item_existed( $post_id ) {
		global $wpdb;
		return $wpdb->get_var( $wpdb->prepare( "SELECT EXISTS( SELECT 1 FROM {$wpdb->ps_posts} WHERE post_id = %d LIMIT 0,1 )", $post_id ) );
	}

	/**
	 * Get Predictive Search Latest Post ID
	 */
	public function get_latest_post_id( $post_types = array() ) {
		global $wpdb;

		if ( empty( $post_types ) ) {
			return $wpdb->get_var( "SELECT post_id FROM {$wpdb->ps_posts} ORDER BY post_id DESC LIMIT 0,1" );
		} else {
			return $wpdb->get_var( "SELECT post_id FROM {$wpdb->ps_posts} WHERE post_type IN ('". implode("','", $post_types ) ."') ORDER BY post_id DESC LIMIT 0,1" );
		}
	}

	/**
	 * Check Latest Post ID is newest from WP database
	 */
	public function is_newest_id( $post_types = array() ) {
		global $wpdb;

		if ( ! is_array( $post_types ) ) {
			$post_types = array( $post_types );
		}

		if ( empty( $post_types ) ) {
			$post_types = array( 'post', 'page', 'product', 'product_variation' );
		}

		$latest_id = $this->get_latest_post_id( $post_types );
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
	public function get_total_items_synched( $post_type = '' ) {
		global $wpdb;
		if ( '' == trim( $post_type ) ) {
			return $wpdb->get_var( "SELECT COUNT(post_id) FROM {$wpdb->ps_posts} " );
		} else {
			return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(post_id) FROM {$wpdb->ps_posts} WHERE post_type = %s ", $post_type ) );
		}
	}

	/**
	 * Delete Predictive Search Post
	 */
	public function delete_item( $post_id ) {
		global $wpdb;
		return $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->ps_posts} WHERE post_id = %d ", $post_id ) );
	}

	/**
	 * Empty Predictive Search Posts
	 */
	public function empty_table() {
		global $wpdb;
		return $wpdb->query( "TRUNCATE {$wpdb->ps_posts}" );
	}
}
