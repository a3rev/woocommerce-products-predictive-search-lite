<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */

namespace A3Rev\WCPredictiveSearch;

// File Security Check
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Dashboard_AJAX
{

	public function __construct() {
		$this->add_ajax_events();
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */

	public function add_ajax_events() {
		$ajax_events = array(
			'get_exclude_options' => false,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_wc_ps_' . $ajax_event, array( $this, $ajax_event . '_ajax' ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_wc_ps_' . $ajax_event, array( $this, $ajax_event . '_ajax' ) );
			}
		}
	}



	/*
	 * Main AJAX handle
	 */

	public function get_exclude_options_ajax() {
		check_ajax_referer( 'wc_predictive_search_get_exclude_options', 'security' );

		global $wpdb;

		$keyword = isset( $_GET['keyword']) ? sanitize_text_field( $_GET['keyword'] ) : '';
		$type    = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : 'product';

		if ( empty( $keyword ) ) {
			wp_send_json( array() );
			die();
		}

		$options_data = array();

		switch ( $type ) {
			case 'product':
			case 'post':
			case 'page':
				$search_results = $wpdb->get_results( "SELECT post_id, post_title FROM ".$wpdb->prefix."ps_posts WHERE post_type='{$type}' AND post_title LIKE '%". $keyword . "%' ORDER BY post_title ASC" );
				if ( $search_results ) {
					foreach( $search_results as $item_data ) {
						$options_data[] = array( 'value' => $item_data->post_id, 'caption' => $item_data->post_title );
					}
				}
				break;
			
			case 'product-category':
				$search_results = $wpdb->get_results( "SELECT term_id, name FROM ".$wpdb->prefix."ps_product_categories WHERE name LIKE '%". $keyword . "%' ORDER BY name ASC" );
				if ( $search_results ) {
					foreach( $search_results as $item_data ) {
						$options_data[] = array( 'value' => $item_data->term_id, 'caption' => $item_data->name );
					}
				}
				break;

			case 'product-tag':
				$search_results = $wpdb->get_results( "SELECT term_id, name FROM ".$wpdb->prefix."ps_product_tags WHERE name LIKE '%". $keyword . "%' ORDER BY name ASC" );
				if ( $search_results ) {
					foreach( $search_results as $item_data ) {
						$options_data[] = array( 'value' => $item_data->term_id, 'caption' => $item_data->name );
					}
				}
				break;
		}
		
		wp_send_json( $options_data );

		die();
	}

}
