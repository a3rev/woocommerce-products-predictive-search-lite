<?php
/**
 * WC Predictive Search WPML Functions
 *
 * Table Of Contents
 *
 * plugins_loaded()
 * wpml_register_string()
 */

namespace A3Rev\WCPredictiveSearch;

class WPML_Functions
{	
	public $plugin_wpml_name = 'WooCommerce Predictive Search';
	
	public function __construct() {
		
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
		
		$this->wpml_ict_t();
		
	}
	
	/** 
	 * Register WPML String when plugin loaded
	 */
	public function plugins_loaded() {
		$this->wpml_register_dynamic_string();
		$this->wpml_register_static_string();
	}
	
	/** 
	 * Get WPML String when plugin loaded
	 */
	public function wpml_ict_t() {
		
		$plugin_name = WOOPS_KEY;

		add_filter( $plugin_name . '_' . 'wc_predictive_search_sidebar_template_settings' . '_get_settings', array( $this, 'ict_t_sidebar_template_settings' ) );
		
	}
	
	// Registry Dynamic String for WPML
	public function wpml_register_dynamic_string() {
		global $wc_predictive_search_admin_interface;

		$wc_predictive_search_sidebar_template_settings = array_map( array( $wc_predictive_search_admin_interface, 'admin_stripslashes' ), get_option( 'wc_predictive_search_sidebar_template_settings', array() ) );
		
		if ( function_exists('icl_register_string') ) {
			icl_register_string($this->plugin_wpml_name, 'More result Text - Sidebar', $wc_predictive_search_sidebar_template_settings['sidebar_popup_seemore_text'] );
		}
	}
	
	// Registry Static String for WPML
	public function wpml_register_static_string() {
		if ( function_exists('icl_register_string') ) {
			
			// Default Form
			icl_register_string( $this->plugin_wpml_name, 'Product Name', __( 'Product Name', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Product SKU', __( 'Product SKU', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Product Categories', __( 'Product Categories', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Product Tags', __( 'Product Tags', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Posts', __( 'Posts', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Pages', __( 'Pages', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'SKU', __( 'SKU', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Priced', __( 'Priced', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Price', __( 'Price', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Category', __( 'Category', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Tags', __( 'Tags', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Nothing found', __( 'Nothing found for "%s".', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Last Found', __( 'Showing results for last found search term "%s".', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Viewing all', __( 'Viewing all', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Search Result Text', __( 'search results for your search query', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Sort Text', __( 'Sort Search Results by', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Loading Text', __( 'Loading More Results...', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'No More Result Text', __( 'No More Results to Show', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'Fetching Text', __( 'Fetching search results...', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'No Fetching Result Text', __( 'No Results to Show', 'woocommerce-predictive-search' ) );
			icl_register_string( $this->plugin_wpml_name, 'No Result Text', __( 'Nothing Found! Please refine your search and try again.', 'woocommerce-predictive-search' ) );
		}
	}

	public function ict_t_sidebar_template_settings( $current_settings = array() ) {
		if ( is_array( $current_settings ) && isset( $current_settings['sidebar_popup_seemore_text'] ) ) 
			$current_settings['sidebar_popup_seemore_text'] = ( function_exists('icl_t') ? icl_t( $this->plugin_wpml_name, 'More result Text - Sidebar', $current_settings['sidebar_popup_seemore_text'] ) : $current_settings['sidebar_popup_seemore_text'] );

		return $current_settings;
	}


}
