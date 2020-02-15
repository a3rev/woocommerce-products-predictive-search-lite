<?php
/*
Plugin Name: Predictive Search for WooCommerce
Plugin URI: https://a3rev.com/shop/woocommerce-predictive-search/
Description: With WooCommerce Predictive Search Lite you can add an awesome Predictive Products Search widget to any widgetized area on your site.
Version: 5.2.1
Author: a3rev Software
Author URI: https://www.a3rev.com/
Requires at least: 4.9
Tested up to: 5.3.2
Text Domain: woocommerce-predictive-search
WC requires at least: 3.0.0
WC tested up to: 3.9.2
Domain Path: /languages
License: GPLv2 or later

	WooCommerce Predictive Search. Plugin for the WooCommerce plugin.
	Copyright © 2011 A3 Revolution Software Development team

	A3 Revolution Software Development team
	admin@a3rev.com
	PO Box 1170
	Gympie 4570
	QLD Australia
*/
?>
<?php
define( 'WOOPS_FILE_PATH', dirname(__FILE__) );
define( 'WOOPS_DIR_NAME', basename(WOOPS_FILE_PATH) );
define( 'WOOPS_FOLDER', dirname(plugin_basename(__FILE__)) );
define( 'WOOPS_NAME', plugin_basename(__FILE__) );
define( 'WOOPS_URL', untrailingslashit( plugins_url( '/', __FILE__ ) ) );
define( 'WOOPS_DIR', WP_PLUGIN_DIR . '/' . WOOPS_FOLDER);
define( 'WOOPS_JS_URL',  WOOPS_URL . '/assets/js' );
define( 'WOOPS_CSS_URL',  WOOPS_URL . '/assets/css' );
define( 'WOOPS_IMAGES_URL',  WOOPS_URL . '/assets/images' );
define( 'WOOPS_TEMPLATE_PATH', WOOPS_FILE_PATH . '/templates' );

if(!defined("WOO_PREDICTIVE_SEARCH_DOCS_URI"))
    define("WOO_PREDICTIVE_SEARCH_DOCS_URI", "https://docs.a3rev.com/user-guides/woocommerce/woo-predictive-search/");

define( 'WOOPS_KEY', 'woo_predictive_search' );
define( 'WOOPS_PREFIX', 'wc_predictive_search_' );
define( 'WOOPS_VERSION', '5.2.1' );
define( 'WOOPS_G_FONTS', true );

use \A3Rev\WCPredictiveSearch\FrameWork;

if ( version_compare( PHP_VERSION, '5.6.0', '>=' ) ) {
	require __DIR__ . '/vendor/autoload.php';

	// Predictive Search API
	global $wc_ps_legacy_api;
	$wc_ps_legacy_api = new \A3Rev\WCPredictiveSearch\Legacy_API();

	global $wc_ps_dashboard_ajax;
	$wc_ps_dashboard_ajax = new \A3Rev\WCPredictiveSearch\Dashboard_AJAX();


	// Predictive WPML
	global $wc_predictive_search_wpml;
	$wc_predictive_search_wpml = new \A3Rev\WCPredictiveSearch\WPML_Functions();


	/**
	 * Plugin Framework init
	 */
	global ${WOOPS_PREFIX.'admin_interface'};
	${WOOPS_PREFIX.'admin_interface'} = new FrameWork\Admin_Interface();

	global $wc_admin_predictive_search_page;
	$wc_admin_predictive_search_page = new FrameWork\Pages\Predictive_Search();

	global ${WOOPS_PREFIX.'admin_init'};
	${WOOPS_PREFIX.'admin_init'} = new FrameWork\Admin_Init();

	global ${WOOPS_PREFIX.'less'};
	${WOOPS_PREFIX.'less'} = new FrameWork\Less_Sass();

	// End - Plugin Framework init


	// Predictive Datas
	global $wc_ps_product_sku_data;
	$wc_ps_product_sku_data = new \A3Rev\WCPredictiveSearch\Data\SKU();

	global $wc_ps_postmeta_data;
	$wc_ps_postmeta_data = new \A3Rev\WCPredictiveSearch\Data\PostMeta();

	global $wc_ps_exclude_data;
	$wc_ps_exclude_data = new \A3Rev\WCPredictiveSearch\Data\Exclude();

	global $wc_ps_posts_data;
	$wc_ps_posts_data = new \A3Rev\WCPredictiveSearch\Data\Posts();

	// Predictive Main
	global $wc_predictive_search;
	$wc_predictive_search = new \A3Rev\WCPredictiveSearch\Main();

	// Predictive Error Logs
	global $wc_ps_errors_log;
	$wc_ps_errors_log = new \A3Rev\WCPredictiveSearch\Errors_Log();

	// Predictive Back Bone
	global $wc_ps_hook_backbone;
	$wc_ps_hook_backbone = new \A3Rev\WCPredictiveSearch\Hook_Backbone();

	// Predictive Schedule & Sync
	global $wc_ps_sync;
	$wc_ps_sync = new \A3Rev\WCPredictiveSearch\Sync();

	global $wc_ps_schedule;
	$wc_ps_schedule = new \A3Rev\WCPredictiveSearch\Schedule();

} else {
	return;
}

/**
 * Load Localisation files.
 *
 * Note: the first-loaded translation file overrides any following ones if the same translation is present.
 *
 * Locales found in:
 * 		- WP_LANG_DIR/woocommerce-predictive-search/woocommerce-predictive-search-LOCALE.mo
 * 	 	- WP_LANG_DIR/plugins/woocommerce-predictive-search-LOCALE.mo
 * 	 	- /wp-content/plugins/woocommerce-predictive-search/languages/woocommerce-predictive-search-LOCALE.mo (which if not found falls back to)
 */
function wc_predictive_search_plugin_textdomain() {
	$locale = apply_filters( 'plugin_locale', get_locale(), 'woocommerce-predictive-search' );

	load_textdomain( 'woocommerce-predictive-search', WP_LANG_DIR . '/woocommerce-predictive-search/woocommerce-predictive-search-' . $locale . '.mo' );
	load_plugin_textdomain( 'woocommerce-predictive-search', false, WOOPS_FOLDER . '/languages/' );
}

include 'includes/wc-predictive-template-functions.php';

// Editor
include 'tinymce3/tinymce.php';

include 'admin/wc-predictive-search-init.php';


/**
* Call when the plugin is activated
*/
register_activation_hook(__FILE__,'wc_predictive_install');
