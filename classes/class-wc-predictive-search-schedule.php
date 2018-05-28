<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
class WC_Predictive_Search_Schedule
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
		global $wc_ps_synch;

		$wc_ps_synch->wc_predictive_search_start_sync( $this->error_id );

		// Set status of auto synced is 'run' for when cron job start process
		update_option( 'wc_predictive_search_auto_synced_posts_data', 0 );

		wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_products' );
	}

	public function auto_sync_products() {
		global $wc_ps_synch;

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 60 ), 'wc_predictive_search_auto_sync_detect_error' );

		$result = $wc_ps_synch->wc_predictive_search_sync_posts( 'product', $this->error_id );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error' );

		// If status is continue then register sync products again for continue
		// If status is complete then register sync product skus
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_products' );
		} else {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_product_skus' );
		}


	}

	public function auto_sync_product_skus() {
		global $wc_ps_synch;

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 60 ), 'wc_predictive_search_auto_sync_detect_error' );

		$result = $wc_ps_synch->wc_predictive_search_sync_product_skus( $this->error_id );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error' );

		// If status is continue then register sync products skus again for continue
		// If status is complete then register sync product categories
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_product_skus' );
		} else {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_posts' );
		}
	}

	public function auto_sync_product_categories() {
		wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_product_tags' );
	}

	public function auto_sync_product_tags() {
		wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_posts' );
	}

	public function auto_sync_posts() {
		global $wc_ps_synch;

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 60 ), 'wc_predictive_search_auto_sync_detect_error' );

		$result = $wc_ps_synch->wc_predictive_search_sync_posts( 'post', $this->error_id );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error' );

		// If status is continue then register sync posts again for continue
		// If status is complete then register sync pages
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_posts' );
		} else {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_pages' );
		}
	}

	public function auto_sync_pages() {
		global $wc_ps_synch;

		/*
		 * Add single event after 60 minutes when cron job start process for
		 * to send ERROR email to admin if status of auto synced still is not completed
		 */
		wp_schedule_single_event( time() + ( 60 * 60 ), 'wc_predictive_search_auto_sync_detect_error' );

		$result = $wc_ps_synch->wc_predictive_search_sync_posts( 'page', $this->error_id );

		// Remove the event send ERROR email if don't get limited execute time or php error
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error' );

		// If status is continue then register sync pages again for continue
		// If status is complete then register sync relationships
		if ( isset( $result['status'] ) && 'continue' == $result['status'] ) {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_sync_pages' );
		} else {
			wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_end_sync' );
		}
	}

	public function auto_sync_relationships() {
		wp_schedule_single_event( time() + 5, 'wc_predictive_search_auto_end_sync' );
	}

	public function auto_end_sync() {
		update_option( 'wc_predictive_search_synced_posts_data', 1 );

		// Set status of auto sync is 'completed'
		update_option( 'wc_predictive_search_auto_synced_posts_data', 1 );
		update_option( 'wc_predictive_search_auto_synced_completed_time', current_time( 'timestamp' ) );

		// Send Success email to admin
		$this->auto_sync_success_email();

		// Remove the event send ERROR email if synced full database
		wp_clear_scheduled_hook( 'wc_predictive_search_auto_sync_detect_error' );
	}

	public function auto_sync_detect_error() {
		$auto_synced_completed = get_option( 'wc_predictive_search_auto_synced_posts_data', 1 );

		// If status of auto sync still is not 'completed' then send Error email to admin
		if ( 0 == $auto_synced_completed ) {
			$this->auto_sync_error_email();
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

	public function auto_sync_error_email() {
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
		$content = '<p>'. __( 'There was a problem with the Daily Predictive Search full Database sync. It has NOT COMPLETED for the site', 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', home_url(), home_url() );
		$content .= '</p>';

		$content .= '<p>'. __( 'Please log into the site and run a manual sync', 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', admin_url( 'admin.php?page=woo-predictive-search&tab=performance-settings&box_open=predictive_search_synch_data' ), admin_url( 'admin.php?page=woo-predictive-search&tab=performance-settings&box_open=predictive_search_synch_data' ) );
		$content .= '</p>';

		$content .= '<p>'. __( "If the sync won't complete or the auto sync fails again tomorrow, please raise a support ticket on the plugins forum.", 'woocommerce-predictive-search' );
		$content .= sprintf( '<br><a href="%s" target="_blank">%s</a>', 'https://a3rev.com/forums/forum/woocommerce-plugins/predictive-search/', 'https://a3rev.com/forums/forum/woocommerce-plugins/predictive-search/' );
		$content .= '</p>';

		$content .= '<p>'. __( 'NOTE! You must be logged into your a3rev Software customer account to be able to post a support request.', 'woocommerce-predictive-search' );
		$content .= '</p>';

		wp_mail( $to_email, $subject, $content, $headers, '' );
	}

}

global $wc_ps_schedule;
$wc_ps_schedule = new WC_Predictive_Search_Schedule();
?>