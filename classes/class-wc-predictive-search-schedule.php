<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\WCPredictiveSearch;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;

class Schedule
{
	public $error_id = 'auto_sync';

	public function __construct() {

		// Register the schedule
		add_action( 'init', array( $this, 'register_schedule' ) );
	}

	public function register_schedule() {
		$allow_auto_sync_data = get_option( 'woocommerce_search_allow_auto_sync_data', 'yes' );

		if ( 'yes' == $allow_auto_sync_data ) {
			if ( ! wp_next_scheduled( 'wc_predictive_search_sync_data_scheduled_jobs' ) ) {
				$next_day = date( 'Y-m-d', strtotime('+1 day') );
				$next_time = strtotime( $next_day . ' 00:00:00' );
				$next_time = get_option( 'gmt_offset' ) > 0 ? $next_time - ( 60 * 60 * get_option( 'gmt_offset' ) ) : $next_time +
( 60 * 60 * get_option( 'gmt_offset' ) );

				wp_schedule_event( $next_time, 'daily', 'wc_predictive_search_sync_data_scheduled_jobs' );
			}

			// Hook for run daily
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

			// Detect if auto sync is ERROR
			add_action( 'wc_predictive_search_auto_sync_detect_error', array( $this, 'auto_sync_detect_error' ) );
		} else {
			wp_clear_scheduled_hook( 'wc_predictive_search_sync_data_scheduled_jobs' );
		}
	}

	public function auto_sync_search_data() {
		global $wc_ps_sync;

		$wc_ps_sync->wc_predictive_search_start_sync( $this->error_id, 'auto' );

		// Set status of auto synced is 'run' for when cron job start process
		update_option( 'wc_predictive_search_auto_synced_full_data_successed', 0 );

		wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_products' );
	}

	public function auto_sync_products() {
		global $wc_ps_sync;

		$is_starting_manual_sync = get_transient( 'wc_predictive_search_starting_manual_sync' );
		if ( false !== $is_starting_manual_sync && (int) $is_starting_manual_sync > time() ) {
			return;
		}

		$is_synced_successed = get_option( 'wc_predictive_search_auto_synced_posts_table_successed', 0 );

		if ( 1 == $is_synced_successed ) {
			global $wc_ps_posts_data;
			global $wc_ps_postmeta_data;

			$wc_ps_posts_data->empty_table();
			$wc_ps_postmeta_data->empty_table();

			update_option( 'wc_predictive_search_auto_synced_posts_table_successed', 0 );
		}

		$is_products_synced_successed = get_option( 'wc_predictive_search_auto_synced_products_successed', 0 );
		if ( 1 == $is_products_synced_successed ) {
			update_option( 'wc_predictive_search_auto_synced_products_successed', 0 );
		} else {
			add_option( 'wc_predictive_search_auto_synced_products_successed', 0 );
		}

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 5 ), 'wc_predictive_search_auto_sync_detect_error', array( 'products' ) );

		$result = $wc_ps_sync->wc_predictive_search_sync_posts( 'product', $this->error_id, 'auto' );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'products' ) );

		// If status is continue then register sync products again for continue
		// If status is complete then register sync product skus
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_products' );
		} else {
			// Set status as successed before sync next object
			update_option( 'wc_predictive_search_auto_synced_products_successed', 1 );

			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_product_skus' );
		}


	}

	public function auto_sync_product_skus() {
		global $wc_ps_sync;

		$is_starting_manual_sync = get_transient( 'wc_predictive_search_starting_manual_sync' );
		if ( false !== $is_starting_manual_sync && (int) $is_starting_manual_sync > time() ) {
			return;
		}

		$is_synced_successed = get_option( 'wc_predictive_search_auto_synced_skus_successed', 0 );

		if ( 1 == $is_synced_successed ) {
			global $wc_ps_product_sku_data;

			$wc_ps_product_sku_data->empty_table();
			update_option( 'wc_predictive_search_auto_synced_skus_successed', 0 );
		} else {
			add_option( 'wc_predictive_search_auto_synced_skus_successed', 0 );
		}

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 5 ), 'wc_predictive_search_auto_sync_detect_error', array( 'product_skus' ) );

		$result = $wc_ps_sync->wc_predictive_search_sync_product_skus( $this->error_id, 'auto' );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'product_skus' ) );

		// If status is continue then register sync products skus again for continue
		// If status is complete then register sync product categories
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_product_skus' );
		} else {
			// Set status as successed before sync next object
			update_option( 'wc_predictive_search_auto_synced_skus_successed', 1 );

			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_posts' );
		}
	}

	public function auto_sync_product_categories() {
		wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_product_tags' );
	}

	public function auto_sync_product_tags() {
		wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_posts' );
	}

	public function auto_sync_posts() {
		global $wc_ps_sync;

		$is_starting_manual_sync = get_transient( 'wc_predictive_search_starting_manual_sync' );
		if ( false !== $is_starting_manual_sync && (int) $is_starting_manual_sync > time() ) {
			return;
		}

		$is_posts_synced_successed = get_option( 'wc_predictive_search_auto_synced_posts_successed', 0 );
		if ( 1 == $is_posts_synced_successed ) {
			update_option( 'wc_predictive_search_auto_synced_posts_successed', 0 );
		} else {
			add_option( 'wc_predictive_search_auto_synced_posts_successed', 0 );
		}

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 5 ), 'wc_predictive_search_auto_sync_detect_error', array( 'posts' ) );

		$result = $wc_ps_sync->wc_predictive_search_sync_posts( 'post', $this->error_id, 'auto' );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'posts' ) );

		// If status is continue then register sync posts again for continue
		// If status is complete then register sync pages
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_posts' );
		} else {
			// Set status as successed before sync next object
			update_option( 'wc_predictive_search_auto_synced_posts_successed', 1 );

			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_pages' );
		}
	}

	public function auto_sync_pages() {
		global $wc_ps_sync;

		$is_starting_manual_sync = get_transient( 'wc_predictive_search_starting_manual_sync' );
		if ( false !== $is_starting_manual_sync && (int) $is_starting_manual_sync > time() ) {
			return;
		}

		$is_pages_synced_successed = get_option( 'wc_predictive_search_auto_synced_pages_successed', 0 );
		if ( 1 == $is_pages_synced_successed ) {
			update_option( 'wc_predictive_search_auto_synced_pages_successed', 0 );
		} else {
			add_option( 'wc_predictive_search_auto_synced_pages_successed', 0 );
		}

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 5 ), 'wc_predictive_search_auto_sync_detect_error', array( 'pages' ) );

		$result = $wc_ps_sync->wc_predictive_search_sync_posts( 'page', $this->error_id, 'auto' );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'pages' ) );

		// If status is continue then register sync pages again for continue
		// If status is complete then register sync relationships
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_pages' );
		} else {
			// Set status as successed before sync next object
			update_option( 'wc_predictive_search_auto_synced_pages_successed', 1 );
			update_option( 'wc_predictive_search_auto_synced_posts_table_successed', 1 );

			wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_end_sync' );
		}
	}

	public function auto_sync_relationships() {
		wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_end_sync' );
	}

	public function auto_end_sync() {
		update_option( 'wc_predictive_search_synced_posts_data', 1 );

		// Set status of auto sync is 'completed'
		update_option( 'wc_predictive_search_auto_synced_full_data_successed', 1 );
		update_option( 'wc_predictive_search_auto_synced_completed_time', current_time( 'timestamp' ) );

		// Send Success email to admin
		$this->auto_sync_success_email();
	}

	public function auto_sync_detect_error( $type = 'products' ) {
		global $wc_ps_errors_log;

		$is_starting_manual_sync = get_transient( 'wc_predictive_search_starting_manual_sync' );
		if ( false !== $is_starting_manual_sync && (int) $is_starting_manual_sync > time() ) {
			return;
		}

		$auto_synced_completed = get_option( 'wc_predictive_search_auto_synced_full_data_successed', 1 );
		$auto_synced_error_log = trim( $wc_ps_errors_log->get_error( 'auto_sync' ) );

		// If status of auto sync still is not 'completed' then send Error email to admin
		if ( 0 == $auto_synced_completed ) {

			if ( ! empty( $auto_synced_error_log ) ) {
				$this->auto_sync_error_email( $auto_synced_error_log );
			} else {

				// Continue register child single event if don't have any error ( for cause it's stopped by upgrade theme or plugin or core WordPress )
				wp_schedule_single_event( time() - 5, 'wc_predictive_search_auto_sync_' . $type );
			}
			
		}
	}

	public function auto_sync_success_email() {
		$to_email = get_option( 'woocommerce_search_schedule_success_recipients', '' );

		// Don't send email if don't have any recipients
		if ( '' == trim( $to_email ) ) {
			return;
		}

		$from_email = get_option( 'admin_email' );
		$from_name = get_option( 'blogname' );

		$headers = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset='. get_option('blog_charset');
		$headers[] = 'From: '.$from_name.' <'.$from_email.'>';

		$subject = sprintf( __( 'Predictive Search Database Synced Completed: %s', 'woocommerce-predictive-search' ), home_url() );
		$content = '<p>' . __( 'Daily Predictive Search full Database sync has been successfully completed for the site', 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', home_url(), home_url() );
		$content .= '</p>';

		wp_mail( $to_email, $subject, $content, $headers, '' );
	}

	public function auto_sync_error_email( $error_log = '' ) {

		if ( empty( $error_log ) ) {
			return false;
		}

		$to_email = get_option( 'woocommerce_search_schedule_error_recipients', '' );

		// Don't send email if don't have any recipients
		if ( '' == trim( $to_email ) ) {
			$to_email = get_option( 'admin_email' );
		}

		$from_email = get_option( 'admin_email' );
		$from_name = get_option( 'blogname' );

		$headers = array();
		$headers[] = 'MIME-Version: 1.0';
		$headers[] = 'Content-type: text/html; charset='. get_option('blog_charset');
		$headers[] = 'From: '.$from_name.' <'.$from_email.'>';

		$subject = sprintf( __( 'Predictive Search Database Sync ERROR: %s', 'woocommerce-predictive-search' ), home_url() );
		$content = '<p>'. __( 'There was a problem with the Scheduled WooCommerce Predictive Search Database sync. It did NOT COMPLETE on the site:', 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', home_url(), home_url() );
		$content .= '</p>';

		$content = '<p>'. __( 'Error log for Debugging:', 'woocommerce-predictive-search' );
		$content .= '<br>'. $error_log;
		$content .= '</p>';

		$content .= '<p>'. __( 'Please login to the site and try running a manual sync', 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', admin_url( 'admin.php?page=woo-predictive-search&tab=performance-settings&box_open=predictive_search_synch_data' ), admin_url( 'admin.php?page=woo-predictive-search&tab=performance-settings&box_open=predictive_search_synch_data' ) );
		$content .= '</p>';

		$content .= '<p>'. __( "If the manual sync won't complete or it fails again tomorrow, please open a support ticket and copy and paste the error log into the ticket.", 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', $GLOBALS[WOOPS_PREFIX.'admin_init']->support_url, $GLOBALS[WOOPS_PREFIX.'admin_init']->support_url );
		$content .= '</p>';

		wp_mail( $to_email, $subject, $content, $headers, '' );
	}

	public function stop_child_schedule_events_auto_sync() {
		set_transient( 'wc_predictive_search_starting_manual_sync', time() + 60, 60 * 5 );

		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_products' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_product_skus' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_product_categories' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_product_tags' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_posts' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_pages' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_relationships' );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_end_sync' );

		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'products' ) );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'product_skus' ) );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'product_categories' ) );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'product_tags' ) );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'posts' ) );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'pages' ) );
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error', array( 'relationships' ) );

		delete_option( 'wc_predictive_search_auto_synced_posts_table_successed' );
		delete_option( 'wc_predictive_search_auto_synced_skus_successed' );
		delete_option( 'wc_predictive_search_auto_synced_product_categories_successed' );
		delete_option( 'wc_predictive_search_auto_synced_product_tags_successed' );
		delete_option( 'wc_predictive_search_auto_synced_relationships_successed' );
	}

}
