<?php
/* "Copyright 2012 A3 Revolution Web Design" This software is distributed under the terms of GNU GENERAL PUBLIC LICENSE Version 3, 29 June 2007 */
/**
 * WooCommerce Predictive Search Legacy API Class
 *
 */

namespace A3Rev\WCPredictiveSearch;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Legacy_API {

	/** @var string $base the route base */
	protected $base = '/wc_ps_legacy_api';
	protected $base_tag = 'wc_ps_legacy_api';

	/**
	* Default contructor
	*/
	public function __construct() {
		add_action( 'woocommerce_api_' . $this->base_tag, array( $this, 'wc_ps_api_handler' ) );
	}

	public function get_legacy_api_url() {

		$legacy_api_url = \WC()->api_request_url( $this->base_tag );
		$legacy_api_url = str_replace( array( 'https:', 'http:' ), '', $legacy_api_url );

		return apply_filters( 'wc_ps_legacy_api_url', $legacy_api_url );
	}

	public function wc_ps_api_handler() {
		if ( isset( $_REQUEST['action'] ) ) {
			$action = addslashes( trim( $_REQUEST['action'] ) );
			switch ( $action ) {
				case 'get_result_popup' :
					$this->get_result_popup();
				break;

				case 'get_results' :
					$this->get_all_results();
				break;
			}
		}
	}

	public function get_result_popup() {
		@ini_set('display_errors', false );
		global $woocommerce_search_page_id;
		global $wc_predictive_search;

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = sanitize_text_field( $_REQUEST['ps_lang'] );
		}

		$rs_items = array();
		$row = 6;
		$text_lenght = 100;
		$show_price = 0;
		$show_addtocart = 0;
		$show_in_cat = 0;
		$search_keyword = '';
		$last_found_search_term = '';
		$cat_in = 'all';
		$widget_template = 'sidebar';
		$found_items = false;
		$total_product = $total_post = $total_page = 0;
		$items_search_default = Widgets::get_items_search();
		$search_in_default = array();
		foreach ( $items_search_default as $key => $data ) {
			if ( $data['number'] > 0 ) {
				$search_in_default[$key] = $data['number'];
			}
		}
		if ( isset($_REQUEST['row']) && $_REQUEST['row'] > 0) $row = sanitize_text_field( wp_unslash( $_REQUEST['row'] ) );
		if ( isset($_REQUEST['text_lenght']) && $_REQUEST['text_lenght'] >= 0) $text_lenght = sanitize_text_field( wp_unslash( $_REQUEST['text_lenght'] ) );
		if ( isset($_REQUEST['show_price']) && trim($_REQUEST['show_price']) != '') $show_price = sanitize_text_field( wp_unslash( $_REQUEST['show_price'] ) );
		if ( $show_price == 1 ) $show_price = true; else $show_price = false;
		if ( isset($_REQUEST['show_addtocart']) && trim($_REQUEST['show_addtocart']) != '') $show_addtocart = sanitize_text_field( wp_unslash( $_REQUEST['show_addtocart'] ) );
		if ( $show_addtocart == 1 ) $show_addtocart = true; else $show_addtocart = false;
		if ( isset($_REQUEST['show_in_cat']) && trim($_REQUEST['show_in_cat']) != '') $show_in_cat = sanitize_text_field( wp_unslash( $_REQUEST['show_in_cat'] ) );
		if ( $show_in_cat == 1 ) $show_in_cat = true; else $show_in_cat = false;
		if ( isset($_REQUEST['q']) && trim($_REQUEST['q']) != '') $search_keyword = sanitize_text_field( wp_unslash( $_REQUEST['q'] ) );
		if ( isset($_REQUEST['cat_in']) && trim($_REQUEST['cat_in']) != '') $cat_in = sanitize_text_field( wp_unslash( $_REQUEST['cat_in'] ) );
		if ( isset($_REQUEST['search_in']) && trim($_REQUEST['search_in']) != '') $search_in = json_decode( sanitize_text_field( wp_unslash( $_REQUEST['search_in'] ) ), true );
		if ( ! is_array($search_in) || count($search_in) < 1 || array_sum($search_in) < 1) $search_in = $search_in_default;
		if ( isset($_REQUEST['widget_template']) && trim($_REQUEST['widget_template']) != '' ) $widget_template = sanitize_key( wp_unslash( $_REQUEST['widget_template'] ) );

		if ( isset($_REQUEST['last_search_term']) && trim($_REQUEST['last_search_term']) != '') $last_found_search_term = sanitize_text_field( wp_unslash( $_REQUEST['last_search_term'] ) );

		if ( $search_keyword != '' ) {
			$search_list = array();
			foreach ($search_in as $key => $number) {
				if ( ! isset( $items_search_default[$key] ) ) continue;
				if ($number > 0)
					$search_list[$key] = $key;
			}

			$woocommerce_search_focus_enable = false;
			$woocommerce_search_focus_plugin = false;

			$all_items = array();
			$product_list = array();
			$post_list = array();
			$page_list = array();

			$permalink_structure = get_option( 'permalink_structure' );

			$product_term_id = 0;
			$post_term_id = 0;

			if ( isset( $search_in['product'] ) && $search_in['product'] > 0 ) {
				$product_list = $wc_predictive_search->get_product_results( $search_keyword, $search_in['product'], 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $product_term_id, $text_lenght, $current_lang, true, $show_price, true, $show_addtocart, $show_in_cat );
				$total_product = $product_list['total'];
				if ( $total_product > 0 ) {
					$found_items = true;
					$rs_items['product'] = $product_list['items'];
				}
			}

			if ( isset( $search_in['post'] ) && $search_in['post'] > 0 ) {
				$post_list = $wc_predictive_search->get_post_results( $search_keyword, $search_in['post'], 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_term_id, $text_lenght, $current_lang, 'post', true, $show_in_cat );
				$total_post = $post_list['total'];
				if ( $total_post > 0 ) {
					$found_items = true;
					$rs_items['post'] = $post_list['items'];
				}
			}

			if ( isset( $search_in['page'] ) && $search_in['page'] > 0 ) {
				$page_list = $wc_predictive_search->get_post_results( $search_keyword, $search_in['page'], 0, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 0, $text_lenght, $current_lang, 'page' );
				$total_page = $page_list['total'];
				if ( $total_page > 0 ) {
					$found_items = true;
					$rs_items['page'] = $page_list['items'];
				}
			}

			if ( $found_items === false ) {
				$nothing_title = sprintf( wc_ps_ict_t__( 'Nothing found', __('Nothing found for "%s".', 'woocommerce-predictive-search' ) ), $search_keyword );

				if ( '' != $last_found_search_term && $last_found_search_term != $search_keyword ) {
					$nothing_title .= ' ' . sprintf( wc_ps_ict_t__( 'Last Found', __('Showing results for last found search term "%s".', 'woocommerce-predictive-search' ) ), $last_found_search_term );
				}
				$all_items[] = array(
					'title' 	=> $nothing_title,
					'keyword'	=> $search_keyword,
					'type'		=> 'nothing'
				);
			} else {
				foreach ( $search_in as $key => $number ) {
					if ( $number > 0 ) {
						if ( isset( $rs_items[$key] ) ) $all_items = array_merge( $all_items, $rs_items[$key] );
					}
				}

				$search_other = $search_list;
				if ( $total_product < 1 )  { unset($search_list['product']); unset($search_other['product']);
				} elseif ($total_product <= $search_in['product']) { unset($search_list['product']); }

				if ( $total_post < 1 ) { unset($search_list['post']); unset($search_other['post']);
				} elseif ($total_post <= $search_in['post']) { unset($search_list['post']); }

				if ( $total_page < 1 ) { unset($search_list['page']); unset($search_other['page']);
				} elseif ($total_page <= $search_in['page']) { unset($search_list['page']); }

				if ( count( $search_list ) > 0 ) {
					$rs_footer_html = '';
					foreach ($search_list as $other_rs) {
						if ( $permalink_structure == '')
							$search_in_parameter = '&search_in='.$other_rs;
						else
							$search_in_parameter = '/search-in/'.$other_rs;
						if ( $permalink_structure == '')
							$link_search = get_permalink( $woocommerce_search_page_id ).'&rs='. urlencode($search_keyword) .$search_in_parameter.'&search_other='.implode(",", $search_other).'&cat_in='.$cat_in;
						else
							$link_search = rtrim( get_permalink( $woocommerce_search_page_id ), '/' ).'/keyword/'. urlencode($search_keyword) .$search_in_parameter.'/cat-in/'.$cat_in.'/search-other/'.implode(",", $search_other);
						$rs_item = '<a href="'.$link_search.'">'.$items_search_default[$other_rs]['name'].'<div class="see_more_arrow" aria-label="'.__( 'View More', 'woocommerce-predictive-search' ).'"><svg viewBox="0 0 256 512" height="12" width="12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" style="display: inline-block; vertical-align: middle;"><path d="M224.3 273l-136 136c-9.4 9.4-24.6 9.4-33.9 0l-22.6-22.6c-9.4-9.4-9.4-24.6 0-33.9l96.4-96.4-96.4-96.4c-9.4-9.4-9.4-24.6 0-33.9L54.3 103c9.4-9.4 24.6-9.4 33.9 0l136 136c9.5 9.4 9.5 24.6.1 34z"></path></svg></div></a>';
						$rs_footer_html .= "$rs_item";
					}

					$footertype = 'footerSidebar';
					if ( 'header' == $widget_template ) {
						$footertype = 'footerHeader';
					}
					$all_items[] = array(
						'title' 	=> $search_keyword,
						'keyword'	=> $search_keyword,
						'description'	=> $rs_footer_html,
						'type'		=> $footertype
					);
				}
			}

			header( 'Content-Type: application/json', true, 200 );
			die( json_encode( $all_items ) );
		} else {
			header( 'Content-Type: application/json', true, 200 );
			die( json_encode( array() ) );
		}

	}

	public function get_all_results() {
		@ini_set('display_errors', false );
		global $wc_predictive_search;

		$current_lang = '';
		if ( class_exists('SitePress') ) {
			$current_lang = sanitize_text_field( $_REQUEST['ps_lang'] );
		}

		$psp = 1;
		$row = 12;
		$search_keyword = '';
		$cat_in = 'all';
		$search_in = 'product';

		if ( get_option('woocommerce_search_result_items') > 0  ) $row = get_option('woocommerce_search_result_items');

		if ( isset( $_REQUEST['psp'] ) && $_REQUEST['psp'] > 0 ) $psp = sanitize_text_field( wp_unslash( $_REQUEST['psp'] ) );
		if ( isset( $_REQUEST['q'] ) && trim( $_REQUEST['q'] ) != '' ) $search_keyword = sanitize_text_field( wp_unslash( $_REQUEST['q'] ) );
		if ( isset( $_REQUEST['cat_in'] ) && trim( $_REQUEST['cat_in'] ) != '' ) $cat_in = sanitize_text_field( wp_unslash( $_REQUEST['cat_in'] ) );
		if ( isset( $_REQUEST['search_in'] ) && trim( $_REQUEST['search_in'] ) != '' ) $search_in = sanitize_text_field( wp_unslash( $_REQUEST['search_in'] ) );

		$item_list = array( 'total' => 0, 'items' => array() );

		if ( $search_keyword != '' && $search_in != '') {
			$show_sku = false;
			$show_price = false;
			$show_addtocart = false;
			$show_categories = false;
			$show_tags = false;
			if ( get_option('woocommerce_search_sku_enable') == '' || get_option('woocommerce_search_sku_enable') == 'yes' ) $show_sku = true;
			if ( get_option('woocommerce_search_price_enable') == '' || get_option('woocommerce_search_price_enable') == 'yes' ) $show_price = true;
			if ( get_option('woocommerce_search_addtocart_enable') == '' || get_option('woocommerce_search_addtocart_enable') == 'yes' ) $show_addtocart = true;
			if ( get_option('woocommerce_search_categories_enable') == '' || get_option('woocommerce_search_categories_enable') == 'yes' ) $show_categories = true;
			if ( get_option('woocommerce_search_tags_enable') == '' || get_option('woocommerce_search_tags_enable') == 'yes' ) $show_tags = true;

			$text_lenght = get_option('woocommerce_search_text_lenght');

			$product_term_id = 0;
			$post_term_id = 0;

			$start = ( $psp - 1) * $row;

			$woocommerce_search_focus_enable = false;
			$woocommerce_search_focus_plugin = false;

			if ( $search_in == 'product' ) {
				$item_list = $wc_predictive_search->get_product_results( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $product_term_id, $text_lenght, $current_lang, false, $show_price, $show_sku, $show_addtocart, $show_categories, $show_tags, true );
			} elseif ( $search_in == 'post' ) {
				$item_list = $wc_predictive_search->get_post_results( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, $post_term_id, $text_lenght, $current_lang, 'post', false , $show_categories, $show_tags );
			} elseif ( $search_in == 'page' ) {
				$item_list = $wc_predictive_search->get_post_results( $search_keyword, $row, $start, $woocommerce_search_focus_enable, $woocommerce_search_focus_plugin, 0, $text_lenght, $current_lang, 'page', false );
			}
		}

		header( 'Content-Type: application/json', true, 200 );
		die( json_encode( $item_list ) );
	}

}
