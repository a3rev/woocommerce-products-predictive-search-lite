<?php
/*
Plugin Name: WooCommerce Predictive Search LITE
Plugin URI: https://a3rev.com/shop/woocommerce-predictive-search/
Description: With WooCommerce Predictive Search Lite you can add an awesome Predictive Products Search widget to any widgetized area on your site.
Version: 4.2.2
Author: a3rev Software
Author URI: https://www.a3rev.com/
Requires at least: 4.5
Tested up to: 5.0.2
Text Domain: woocommerce-predictive-search
WC requires at least: 2.0.0
WC tested up to: 3.5.2
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
define( 'WOOPS_VERSION', '4.2.2' );
define( 'WOOPS_G_FONTS', true );

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

// Predictive Search API
include('includes/class-legacy-api.php');
include('includes/class-dashboard-ajax.php');


include('admin/admin-ui.php');
include('admin/admin-interface.php');

include 'classes/class-wc-predictive-search-functions.php';
include('classes/class-wpml-functions.php');

include('admin/admin-pages/predictive-search-page.php');

include('admin/admin-init.php');
include('admin/less/sass.php');


include 'classes/data/class-wc-ps-product-sku-data.php';
include 'classes/data/class-wc-ps-postmeta-data.php';
include 'classes/data/class-wc-ps-exclude-data.php';
include 'classes/data/class-wc-ps-posts-data.php';

include 'includes/class-wc-predictive-search.php';

include 'includes/wc-predictive-template-functions.php';

include 'classes/class-wc-predictive-search-errors-log.php';
include 'classes/class-wc-predictive-search-filter.php';
include 'classes/class-wc-predictive-search-shortcodes.php';
include 'classes/class-wc-predictive-search-metabox.php';
include 'classes/class-wc-predictive-search-backbone.php';
include 'widget/wc-predictive-search-widgets.php';

include 'classes/class-wc-predictive-search-synch.php';
include 'classes/class-wc-predictive-search-schedule.php';

// Editor
include 'tinymce3/tinymce.php';

include 'admin/wc-predictive-search-init.php';


/**
* Call when the plugin is activated
*/
register_activation_hook(__FILE__,'wc_predictive_install');

?>