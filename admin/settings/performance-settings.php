<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
// File Security Check
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<?php
/*-----------------------------------------------------------------------------------
WC Predictive Search Performance Settings

TABLE OF CONTENTS

- var parent_tab
- var subtab_data
- var option_name
- var form_key
- var position
- var form_fields
- var form_messages

- __construct()
- subtab_init()
- set_default_settings()
- get_settings()
- subtab_data()
- add_subtab()
- settings_form()
- init_form_fields()

-----------------------------------------------------------------------------------*/

class WC_Predictive_Search_Performance_Settings extends WC_Predictive_Search_Admin_UI
{
	
	/**
	 * @var string
	 */
	private $parent_tab = 'performance-settings';
	
	/**
	 * @var array
	 */
	private $subtab_data;
	
	/**
	 * @var string
	 * You must change to correct option name that you are working
	 */
	public $option_name = '';
	
	/**
	 * @var string
	 * You must change to correct form key that you are working
	 */
	public $form_key = 'wc_predictive_search_performance_settings';
	
	/**
	 * @var string
	 * You can change the order show of this sub tab in list sub tabs
	 */
	private $position = 1;
	
	/**
	 * @var array
	 */
	public $form_fields = array();
	
	/**
	 * @var array
	 */
	public $form_messages = array();
	
	/*-----------------------------------------------------------------------------------*/
	/* __construct() */
	/* Settings Constructor */
	/*-----------------------------------------------------------------------------------*/
	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'init_form_fields' ), 1 );
		$this->subtab_init();
		
		$this->form_messages = array(
				'success_message'	=> __( 'Performance Settings successfully saved.', 'woocommerce-predictive-search' ),
				'error_message'		=> __( 'Error: Performance Settings can not save.', 'woocommerce-predictive-search' ),
				'reset_message'		=> __( 'Performance Settings successfully reseted.', 'woocommerce-predictive-search' ),
			);

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'include_script' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_end', array( $this, 'error_logs_container' ) );

		add_action( $this->plugin_name . '_set_default_settings' , array( $this, 'set_default_settings' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_before_settings_save', array( $this, 'before_save_settings' ) );

		add_action( $this->plugin_name . '-' . $this->form_key . '_settings_init' , array( $this, 'after_save_settings' ) );
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* subtab_init() */
	/* Sub Tab Init */
	/*-----------------------------------------------------------------------------------*/
	public function subtab_init() {
		
		add_filter( $this->plugin_name . '-' . $this->parent_tab . '_settings_subtabs_array', array( $this, 'add_subtab' ), $this->position );
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* set_default_settings()
	/* Set default settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function set_default_settings() {
		global $wc_predictive_search_admin_interface;
		
		$wc_predictive_search_admin_interface->reset_settings( $this->form_fields, $this->option_name, false );
	}

	/*-----------------------------------------------------------------------------------*/
	/* before_save_settings()
	/*
	/*-----------------------------------------------------------------------------------*/
	public function before_save_settings() {
		$old_schedule_time = get_option( 'woocommerce_search_schedule_time_sync_data', '00:00' );
		$new_schedule_time = $old_schedule_time;
		if ( isset( $_POST['woocommerce_search_schedule_time_sync_data'] ) && '' != $_POST['woocommerce_search_schedule_time_sync_data'] ) {
			$new_schedule_time = date( 'H:i', strtotime( $_POST['woocommerce_search_schedule_time_sync_data'] ) );
		}

		$new_allow_auto_sync_data = 'yes';
		if ( ! isset( $_POST['woocommerce_search_allow_auto_sync_data'] ) || 'yes' != $_POST['woocommerce_search_allow_auto_sync_data'] ) {
			$new_allow_auto_sync_data = 'no';
		}

		if ( 'no' != $new_allow_auto_sync_data ) {

			/*
			* registered event
			* if Auto Sync Daily is set 'ON' and Schedule Time is changed
			*/
			if ( $old_schedule_time != $new_schedule_time || ! wp_next_scheduled( 'wc_predictive_search_sync_data_scheduled_jobs' ) ) {
				wp_clear_scheduled_hook( 'wc_predictive_search_sync_data_scheduled_jobs' );

				$next_day = date( 'Y-m-d', strtotime('+1 day') );
				$next_time = strtotime( $next_day . ' ' . $new_schedule_time .':00' );
				$next_time = get_option( 'gmt_offset' ) > 0 ? $next_time - ( 60 * 60 * get_option( 'gmt_offset' ) ) : $next_time +
( 60 * 60 * get_option( 'gmt_offset' ) );

				wp_schedule_event( $next_time, 'daily', 'wc_predictive_search_sync_data_scheduled_jobs' );
			}

		} else {

			/*
			* deregistered event
			* if Auto Sync Daily is set 'OFF'
			*/
			wp_clear_scheduled_hook( 'wc_predictive_search_sync_data_scheduled_jobs' );
		}
	}

	/*-----------------------------------------------------------------------------------*/
	/* after_save_settings()
	/* Process when clean on deletion option is un selected */
	/*-----------------------------------------------------------------------------------*/
	public function after_save_settings() {

	}
	
	/*-----------------------------------------------------------------------------------*/
	/* get_settings()
	/* Get settings with function called from Admin Interface */
	/*-----------------------------------------------------------------------------------*/
	public function get_settings() {
		global $wc_predictive_search_admin_interface;
		
		$wc_predictive_search_admin_interface->get_settings( $this->form_fields, $this->option_name );
	}
	
	/**
	 * subtab_data()
	 * Get SubTab Data
	 * =============================================
	 * array ( 
	 *		'name'				=> 'my_subtab_name'				: (required) Enter your subtab name that you want to set for this subtab
	 *		'label'				=> 'My SubTab Name'				: (required) Enter the subtab label
	 * 		'callback_function'	=> 'my_callback_function'		: (required) The callback function is called to show content of this subtab
	 * )
	 *
	 */
	public function subtab_data() {
		
		$subtab_data = array( 
			'name'				=> 'performance-settings',
			'label'				=> __( 'Performance', 'woocommerce-predictive-search' ),
			'callback_function'	=> 'wc_predictive_search_performance_settings_form',
		);
		
		if ( $this->subtab_data ) return $this->subtab_data;
		return $this->subtab_data = $subtab_data;
		
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* add_subtab() */
	/* Add Subtab to Admin Init
	/*-----------------------------------------------------------------------------------*/
	public function add_subtab( $subtabs_array ) {
	
		if ( ! is_array( $subtabs_array ) ) $subtabs_array = array();
		$subtabs_array[] = $this->subtab_data();
		
		return $subtabs_array;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* settings_form() */
	/* Call the form from Admin Interface
	/*-----------------------------------------------------------------------------------*/
	public function settings_form() {
		global $wc_predictive_search_admin_interface;
		
		$output = '';
		$output .= $wc_predictive_search_admin_interface->admin_forms( $this->form_fields, $this->form_key, $this->option_name, $this->form_messages );
		
		return $output;
	}
	
	/*-----------------------------------------------------------------------------------*/
	/* init_form_fields() */
	/* Init all fields of this form */
	/*-----------------------------------------------------------------------------------*/
	public function init_form_fields() {

		$sync_button_text = __( 'Start Sync', 'woocommerce-predictive-search' );
		$synced_full_data = false;
		if ( isset( $_GET['page'] ) && 'woo-predictive-search' == $_GET['page'] && isset( $_GET['tab'] ) && $this->parent_tab == $_GET['tab'] ) {
			if ( ! isset( $_SESSION ) ) {
				@session_start();
			}

			global $wpdb, $wc_ps_product_sku_data;
			$total_products = $wpdb->get_var( $wpdb->prepare( 'SELECT count(id) FROM '.$wpdb->posts.' WHERE post_type=%s AND post_status=%s', 'product', 'publish' ) );
			$total_products = ! empty( $total_products ) ? $total_products : 0;

			$total_skus = $wc_ps_product_sku_data->get_total_items_need_sync();
			$total_skus = ! empty( $total_skus ) ? $total_skus : 0;

			$all_posts      = wp_count_posts( 'post' );
			$total_posts    = isset( $all_posts->publish ) ? $all_posts->publish : 0;

			$all_pages      = wp_count_posts( 'page' );
			$total_pages    = isset( $all_pages->publish ) ? $all_pages->publish : 0;

			global $wc_ps_posts_data;
			$current_products = $wc_ps_posts_data->get_total_items_synched( 'product' );
			$current_skus     = $wc_ps_product_sku_data->get_total_items_synched();
			$current_posts    = $wc_ps_posts_data->get_total_items_synched( 'post' );
			$current_pages    = $wc_ps_posts_data->get_total_items_synched( 'page' );

			$current_categories = 0;
			$total_categories   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(t.term_id) FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy = %s ", 'product_cat' ) );

			$current_tags = 0;
			$total_tags   = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(t.term_id) FROM {$wpdb->terms} AS t INNER JOIN {$wpdb->term_taxonomy} AS tt ON (t.term_id = tt.term_id) WHERE tt.taxonomy = %s ", 'product_tag' ) );

			$current_items = $current_products + $current_skus + $current_posts + $current_pages;
			$total_items   = $total_products + $total_skus + $total_posts + $total_pages;

			$had_sync_posts_data = get_option( 'wc_predictive_search_had_sync_posts_data', 0 );

			if ( 0 == $had_sync_posts_data ) {
				$synced_full_data = true;
				update_option( 'wc_predictive_search_synced_posts_data', 1 );
			} elseif ( $current_items > 0 && $current_items < $total_items ) {
				update_option( 'wc_predictive_search_synced_posts_data', 0 );
				$sync_button_text = __( 'Continue Sync', 'woocommerce-predictive-search' );
			} elseif ( $current_items >= $total_items ) {
				$synced_full_data = true;
				update_option( 'wc_predictive_search_synced_posts_data', 1 );
				$sync_button_text = __( 'Re Sync', 'woocommerce-predictive-search' );
			}
		}

		$auto_synced_completed_time = get_option( 'wc_predictive_search_auto_synced_completed_time', false );
		if ( false !== $auto_synced_completed_time ) {
			$auto_synced_completed_time = date_i18n( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), $auto_synced_completed_time );
		} else {
			$auto_synced_completed_time = '';
		}

		$manual_synced_completed_time = get_option( 'wc_predictive_search_manual_synced_completed_time', false );
		if ( false !== $manual_synced_completed_time ) {
			$manual_synced_completed_time = date_i18n( get_option( 'date_format' ) . ' - ' . get_option( 'time_format' ), $manual_synced_completed_time );
		} else {
			$manual_synced_completed_time = '';
		}

		$have_confirm = false;

		$products_sync_title = __( 'Products Synced', 'woocommerce-predictive-search' ); 
		if ( 0 == get_option( 'wc_predictive_search_auto_synced_products_successed', 1 ) ) {
			$products_sync_title = __( 'Syncing Products...', 'woocommerce-predictive-search' );
			$have_confirm = true;
		}

		$posts_sync_title = __( 'Posts Synced', 'woocommerce-predictive-search' ); 
		if ( 0 == get_option( 'wc_predictive_search_auto_synced_posts_successed', 1 ) ) {
			$posts_sync_title = __( 'Syncing Posts...', 'woocommerce-predictive-search' ); 
			$have_confirm = true;
		}

		$pages_sync_title = __( 'Pages Synced', 'woocommerce-predictive-search' ); 
		if ( 0 == get_option( 'wc_predictive_search_auto_synced_pages_successed', 1 ) ) {
			$pages_sync_title = __( 'Syncing Pages...', 'woocommerce-predictive-search' ); 
			$have_confirm = true;
		}

		// If have Error log on Sync
		global $wc_ps_errors_log;
		$auto_synced_error_log   = trim( $wc_ps_errors_log->get_error( 'auto_sync' ) );

  		// Define settings
     	$this->form_fields = apply_filters( $this->option_name . '_settings_fields', array(

			array(
            	'name' 		=> __( 'Database Sync', 'woocommerce-predictive-search' ),
            	'desc'		=> __( 'Predictive Search database is auto updated whenever a product or post is published or updated. Please run a Manual database sync if you upload products by csv or feel that Predictive Search results are showing old data.  Will sync the Predictive Search database with your current WooCommerce and WordPress databases', 'woocommerce-predictive-search' ),
            	'id'		=> 'predictive_search_synch_data',
                'type' 		=> 'heading',
				'is_box'	=> true,
           	),
           	array(
				'name' 		=> __( 'Auto Sync Daily', 'woocommerce-predictive-search' ),
				'class'		=> 'woocommerce_search_allow_auto_sync_data',
				'id' 		=> 'woocommerce_search_allow_auto_sync_data',
				'type' 		=> 'onoff_checkbox',
				'default'	=> 'yes',
				'checked_value'		=> 'yes',
				'unchecked_value'	=> 'no',
				'checked_label'		=> __( 'ON', 'woocommerce-predictive-search' ),
				'unchecked_label' 	=> __( 'OFF', 'woocommerce-predictive-search' ),
				'separate_option'   => true,
			),
			array(
                'type' 		=> 'heading',
				'class'		=> 'allow_auto_sync_data_container',
           	),
           	array(
				'name' 		=> __( 'Schedule Time', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_schedule_time_sync_data',
				'type' 		=> 'time_picker',
				'default'	=> '00:00',
				'desc'		=> ( '' != $auto_synced_completed_time ? '<span style="color:#46b450;font-style:normal;">' .__( 'Last Scheduled Full Database Sync completed', 'woocommerce-predictive-search' ) . ' ' . $auto_synced_completed_time . '</span>' : '' ) . ( '' != $auto_synced_error_log ? '<span style="color:#f00;font-style:normal; display: block;">' .__( 'ERROR: Latest auto sync has failed to complete', 'woocommerce-predictive-search' ) . ' - <a data-toggle="modal" href="#auto_sync-modal">'. __( 'View Error Log', 'woocommerce-predictive-search' ) .'</a></span>' : '' ),
				'separate_option'   => true,
			),
			array(
                'type' 		=> 'heading',
                'name'		=> __( 'Email Notifications', 'woocommerce-predictive-search' ),
				'class'		=> 'allow_auto_sync_data_container',
           	),
			array(
				'name' 		=> __( 'Error notification recipient(s)', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_schedule_error_recipients',
				'type' 		=> 'text',
				'desc'		=> sprintf( __( 'Blank for default: %s', 'woocommerce-predictive-search' ), get_option( 'admin_email' ) ),
				'separate_option'   => true,
			),
			array(
				'name' 		=> __( 'Sync Complete recipients(s)', 'woocommerce-predictive-search' ),
				'id' 		=> 'woocommerce_search_schedule_success_recipients',
				'type' 		=> 'text',
				'separate_option'   => true,
			),

           	array(
           		'name'		=> __( 'Manual Sync', 'woocommerce-predictive-search' ),
                'type' 		=> 'heading',
           	),
           	array(
				'name'             => __( 'Manual Sync Search Data', 'woocommerce-predictive-search' ),
				'id'               => 'woocommerce_search_sync_data',
				'type'             => 'ajax_multi_submit',
				'statistic_column' => 2,
				'multi_submit' => array(
					array(
						'item_id'          => 'start_sync',
						'item_name'        => __( 'Sync Initied', 'woocommerce-predictive-search' ),
						'current_items'    => 0,
						'total_items'      => 1,
						'progressing_text' => __( 'Start Syncing...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Sync Initied', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_start_sync',
							)
						),
						'show_statistic'       => false,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_products',
						'item_name'        => $products_sync_title,
						'current_items'    => ( ! empty( $current_products ) ) ? (int) $current_products : 0,
						'total_items'      => ( ! empty( $total_products ) ) ? (int) $total_products : 0,
						'progressing_text' => __( 'Syncing Products...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Products', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_products',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_product_skus',
						'item_name'        => __( 'Product SKUs Synced', 'woocommerce-predictive-search' ) . '</span><div style="color: red"><strong>'.__( 'PREMIUM', 'woocommerce-predictive-search' ).'</strong></div><span>',
						'current_items'    => ( ! empty( $current_skus ) ) ? (int) $current_skus : 0,
						'total_items'      => ( ! empty( $total_skus ) ) ? (int) $total_skus : 0,
						'progressing_text' => __( 'Syncing Product SKUs...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Product SKUs', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_product_skus',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_categories',
						'item_name'        => __( 'Product Categories Synced', 'woocommerce-predictive-search' ) . '</span><div style="color: red"><strong>'.__( 'PREMIUM', 'woocommerce-predictive-search' ).'</strong></div><span>',
						'current_items'    => ( ! empty( $current_categories ) ) ? (int) $current_categories : 0,
						'total_items'      => ( ! empty( $total_categories ) ) ? (int) $total_categories : 0,
						'progressing_text' => __( 'Syncing Product Categories...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Product Categories', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_categories',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_tags',
						'item_name'        => __( 'Product Tags Synced', 'woocommerce-predictive-search' ) . '</span><div style="color: red"><strong>'.__( 'PREMIUM', 'woocommerce-predictive-search' ).'</strong></div><span>',
						'current_items'    => ( ! empty( $current_tags ) ) ? (int) $current_tags : 0,
						'total_items'      => ( ! empty( $total_tags ) ) ? (int) $total_tags : 0,
						'progressing_text' => __( 'Syncing Product Tags...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Product Tags', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_tags',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#96587d',
						),
					),
					array(
						'item_id'          => 'sync_posts',
						'item_name'        => $posts_sync_title,
						'current_items'    => ( ! empty( $current_posts ) ) ? (int) $current_posts : 0,
						'total_items'      => ( ! empty( $total_posts ) ) ? (int) $total_posts : 0,
						'progressing_text' => __( 'Syncing Posts...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Posts', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_posts',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#7ad03a',
						)
					),
					array(
						'item_id'          => 'sync_pages',
						'item_name'        => $pages_sync_title,
						'current_items'    => ( ! empty( $current_pages ) ) ? (int) $current_pages : 0,
						'total_items'      => ( ! empty( $total_pages ) ) ? (int) $total_pages : 0,
						'progressing_text' => __( 'Syncing Pages...', 'woocommerce-predictive-search' ),
						'completed_text'   => __( 'Synced Pages', 'woocommerce-predictive-search' ),
						'submit_data'      => array(
							'ajax_url'  => admin_url( 'admin-ajax.php', 'relative' ),
							'ajax_type' => 'POST',
							'data'      => array(
								'action'   => 'wc_predictive_search_sync_pages',
							)
						),
						'show_statistic'       => true,
						'statistic_customizer' => array(
							'current_color' => '#0073aa',
						)
					),
				),
				'separate_option'  => true,
				'button_name'      => $sync_button_text,
				'resubmit'         => $synced_full_data,
				'progressing_text' => __( 'Syncing Data...', 'woocommerce-predictive-search' ),
				'completed_text'   => __( 'Synced Data', 'woocommerce-predictive-search' ),
				'successed_text'   => sprintf( __( 'Last manual Full Database Sync completed <span class="built-time">%s</span>', 'woocommerce-predictive-search' ), $manual_synced_completed_time ),
				'errors_text'      => '<span style="color:#f00;font-style:normal; display: block;">' .__( 'ERROR: Latest manual sync has failed to complete', 'woocommerce-predictive-search' ) . ' - <a data-toggle="modal" href="#manual_sync-modal">'. __( 'View Error Log', 'woocommerce-predictive-search' ) .'</a></span>',
				'confirm_run' 	   => array(
					'allow'   => $have_confirm,
					'message' => __( 'WARNING! Auto sync is currently running. Starting a Manual Sync will cancel the Auto sync and Manual Sync will start from the beginning again. Are you Sure you want to do this?', 'woocommerce-predictive-search' ),
				),
				'notice' 		   => __( 'You need to leave this page open for the sync to complete.', 'woocommerce-predictive-search' ),
			),
        ));
	}

	public function error_logs_container() {
		if ( ! wp_script_is( 'bootstrap-modal', 'registered' ) 
			&& ! wp_script_is( 'bootstrap-modal', 'enqueued' ) ) {
			global $wc_predictive_search_admin_interface;
			$wc_predictive_search_admin_interface->register_modal_scripts();
		}

		wp_enqueue_style( 'bootstrap-modal' );

		// Don't include modal script if bootstrap is loaded by theme or plugins
		if ( wp_script_is( 'bootstrap', 'registered' ) 
			|| wp_script_is( 'bootstrap', 'enqueued' ) ) {
			
			wp_enqueue_script( 'bootstrap' );
			
			return;
		}

		wp_enqueue_script( 'bootstrap-modal' );

		global $wc_ps_errors_log;
		$auto_synced_error_log   = trim( $wc_ps_errors_log->get_error( 'auto_sync' ) );
		$manual_synced_error_log = trim( $wc_ps_errors_log->get_error( 'manual_sync' ) );

		if ( '' != $auto_synced_error_log ) {
			echo $wc_ps_errors_log->error_modal( 'auto_sync', $auto_synced_error_log );
		}

		echo '<div class="manual_sync_error_container">';
		echo $wc_ps_errors_log->error_modal( 'manual_sync', $manual_synced_error_log );
		echo '</div>';
?>
<style type="text/css">
	.a3rev_panel_container .a3rev-ui-ajax_multi_submit-control .a3rev-ui-ajax_multi_submit-errors {
		<?php if ( '' != $manual_synced_error_log ) { ?>
		display: inline;
		<?php } ?>
	}
</style>
<script>
(function($) {

	$(document).ready(function() {

		$(document).on( 'a3rev-ui-ajax_multi_submit-errors', '#woocommerce_search_sync_data', function( event, bt_ajax_submit, multi_ajax ) {
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
				data: { action: 'wc_predictive_search_manual_sync_error' },
				success: function ( response ) {
					$('.manual_sync_error_container').html( response );
				}
			});
		});

	});

})(jQuery);
</script>
<?php
	}

	public function include_script() {
	?>
	<style type="text/css">
		.a3-ps-synched-products {
			color: #96587d;
		}
		.a3-ps-synched-posts {
			color: #7ad03a;
		}
		.a3-ps-synched-pages {
			color: #0073aa;
		}
		<?php 
		$manual_synced_completed_time = get_option( 'wc_predictive_search_manual_synced_completed_time', false );
		if ( false !== $manual_synced_completed_time ) {
		?>
		#predictive_search_synch_data .a3rev-ui-ajax_multi_submit-successed {
			display: inline;
		}
		<?php } ?>
	</style>
<script>
(function($) {

	$(document).ready(function() {

		if ( $("input.woocommerce_search_allow_auto_sync_data:checked").val() != 'yes') {
			$('.allow_auto_sync_data_container').css( {'visibility': 'hidden', 'height' : '0px', 'overflow' : 'hidden', 'margin-bottom' : '0px' } );
		}

		$(document).on( "a3rev-ui-onoff_checkbox-switch", '.woocommerce_search_allow_auto_sync_data', function( event, value, status ) {
			$('.allow_auto_sync_data_container').attr('style','display:none;');
			if ( status == 'true' ) {
				$(".allow_auto_sync_data_container").slideDown();
			}
		});

		$(document).on( 'a3rev-ui-ajax_multi_submit-end', '#woocommerce_search_sync_data', function( event, bt_ajax_submit, multi_ajax ) {
			bt_ajax_submit.html('<?php echo __( 'Re Sync', 'woocommerce-predictive-search' ); ?>');
			$('body').find('.wc_ps_sync_data_warning').slideUp('slow');
			$.ajax({
				type: 'POST',
				url: '<?php echo admin_url( 'admin-ajax.php', 'relative' ); ?>',
				data: { action: 'wc_predictive_search_sync_end' },
				success: function ( response ) {
					$('#predictive_search_synch_data_box_inside').find('.built-time').html( response.date );
				}
			});
		});

	});

})(jQuery);
</script>
    <?php
	}
}

global $wc_predictive_search_performance_settings;
$wc_predictive_search_performance_settings = new WC_Predictive_Search_Performance_Settings();

/** 
 * wc_predictive_search_performance_settings_form()
 * Define the callback function to show subtab content
 */
function wc_predictive_search_performance_settings_form() {
	global $wc_predictive_search_performance_settings;
	$wc_predictive_search_performance_settings->settings_form();
}

?>