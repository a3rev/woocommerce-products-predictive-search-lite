<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_Predictive_Search_Schedule
{
	public function __construct() {

		// Register the schedule
		add_action( 'init', array( $this, 'register_schedule' ) );
	}

	public function register_schedule() {
		$allow_auto_sync_data = get_option( 'woocommerce_search_allow_auto_sync_data', 'yes' );

		if ( 'yes' == $allow_auto_sync_data ) {
			if ( ! wp_next_scheduled( 'wc_predictive_search_sync_data_scheduled_jobs' ) ) {
				$next_day = date( 'Y-m-d', strtotime('+1 day') );
				wp_schedule_event( strtotime( $next_day . ' 00:00:00' ), 'twicedaily', 'wc_predictive_search_sync_data_scheduled_jobs' );
			}

			// Hook for run twice daily
			add_action( 'wc_predictive_search_sync_data_scheduled_jobs', array( $this, 'auto_sync_search_data' ) );

			// Hook for single events
			add_action( 'wc_predictive_search_auto_sync_products', array( $this, 'auto_sync_products' ) );
			add_action( 'wc_predictive_search_auto_sync_product_skus', array( $this, 'auto_sync_product_skus' ) );
			add_action( 'wc_predictive_search_auto_sync_product_categories', array( $this, 'auto_sync_product_categories' ) );
			add_action( 'wc_predictive_search_auto_sync_product_tags', array( $this, 'auto_sync_product_tags' ) );
			add_action( 'wc_predictive_search_auto_sync_posts', array( $this, 'auto_sync_posts' ) );
			add_action( 'wc_predictive_search_auto_sync_pages', array( $this, 'auto_sync_pages' ) );
			add_action( 'wc_predictive_search_auto_sync_relationships', array( $this, 'auto_sync_relationships' ) );
			add_action( 'wc_predictive_search_auto_end_sync', array( $this, 'auto_end_sync' ) );
		} else {
			wp_clear_scheduled_hook( 'wc_predictive_search_sync_data_scheduled_jobs' );
		}
	}

	public function auto_sync_search_data() {
		wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_products' );
	}

	public function auto_sync_products() {
		global $wc_ps_synch;

		// Get statistic of products
		$statistic = $wc_ps_synch->get_sync_posts_statistic( 'product' );

		// If it is complete then register sync product skus and break here
		if ( 'continue' != $statistic['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_product_skus' );
			return;
		}

		update_option( 'wc_predictive_search_synced_posts_data', 0 );

		$result = $wc_ps_synch->wc_predictive_search_sync_posts( 'product' );

		// If status is continue then register sync products again for continue
		// If status is complete then register sync product skus
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_products' );
		} else {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_product_skus' );
		}
	}

	public function auto_sync_product_skus() {
		global $wc_ps_synch;

		// Get statistic of product skus
		$statistic = $wc_ps_synch->get_sync_product_skus_statistic();

		// If it is complete then register sync product categories and break here
		if ( 'continue' != $statistic['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_product_categories' );
			return;
		}

		update_option( 'wc_predictive_search_synced_posts_data', 0 );

		$result = $wc_ps_synch->wc_predictive_search_sync_product_skus();

		// If status is continue then register sync products skus again for continue
		// If status is complete then register sync product categories
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_product_skus' );
		} else {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_product_categories' );
		}
	}

	public function auto_sync_product_categories() {
		wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_product_tags' );
	}

	public function auto_sync_product_tags() {
		wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_posts' );
	}

	public function auto_sync_posts() {
		global $wc_ps_synch;

		// Get statistic of posts
		$statistic = $wc_ps_synch->get_sync_posts_statistic( 'post' );

		// If it is complete then register sync pages and break here
		if ( 'continue' != $statistic['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_pages' );
			return;
		}

		update_option( 'wc_predictive_search_synced_posts_data', 0 );

		$result = $wc_ps_synch->wc_predictive_search_sync_posts( 'post' );

		// If status is continue then register sync posts again for continue
		// If status is complete then register sync pages
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_posts' );
		} else {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_pages' );
		}
	}

	public function auto_sync_pages() {
		global $wc_ps_synch;

		// Get statistic of pages
		$statistic = $wc_ps_synch->get_sync_posts_statistic( 'page' );

		// If it is complete then register sync relationships and break here
		if ( 'continue' != $statistic['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_relationships' );
			return;
		}

		update_option( 'wc_predictive_search_synced_posts_data', 0 );

		$result = $wc_ps_synch->wc_predictive_search_sync_posts( 'page' );

		// If status is continue then register sync pages again for continue
		// If status is complete then register sync relationships
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_pages' );
		} else {
			wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_sync_relationships' );
		}
	}

	public function auto_sync_relationships() {
		wp_schedule_single_event( time() + 20, 'wc_predictive_search_auto_end_sync' );
	}

	public function auto_end_sync() {
		update_option( 'wc_predictive_search_synced_posts_data', 1 );
	}

}

global $wc_ps_schedule;
$wc_ps_schedule = new WC_Predictive_Search_Schedule();
?>