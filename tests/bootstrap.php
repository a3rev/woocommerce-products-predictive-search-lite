<?php
/**
 * PHPUnit bootstrap file
 *
 * @package Sample_Plugin
 */

$_tests_dir = getenv( 'WP_TESTS_DIR' );

if ( ! $_tests_dir ) {
	$_tests_dir = rtrim( sys_get_temp_dir(), '/\\' ) . '/wordpress-tests-lib';
}

if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) ) {
	echo "Could not find $_tests_dir/includes/functions.php, have you run bin/install-wp-tests.sh ?";
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Adds WooCommerce testing framework classes.
 */
function wc_test_includes() {
	$wc_tests_framework_base_dir = wc_dir() . '/tests';

	// WooCommerce test classes.
	// Framework.
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-unit-test-factory.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-session-handler.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-wc-data.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-wc-object-query.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-mock-payment-gateway.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-payment-token-stub.php';
	require_once $wc_tests_framework_base_dir . '/framework/vendor/class-wp-test-spy-rest-server.php';

	// Test cases.
	require_once $wc_tests_framework_base_dir . '/includes/wp-http-testcase.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-unit-test-case.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-api-unit-test-case.php';
	require_once $wc_tests_framework_base_dir . '/framework/class-wc-rest-unit-test-case.php';

	// Helpers.
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-product.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-coupon.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-fee.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-shipping.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-customer.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-order.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-shipping-zones.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-payment-token.php';
	require_once $wc_tests_framework_base_dir . '/framework/helpers/class-wc-helper-settings.php';
}


function wc_dir() {
	static $dir = '';
	if ( $dir === '' ) {
		if ( file_exists( WP_CONTENT_DIR . '/woocommerce/woocommerce.php' ) ) {
			$dir = WP_CONTENT_DIR . '/woocommerce';
			echo "Found WooCommerce plugin in content dir." . PHP_EOL;
		} elseif ( file_exists( dirname( dirname( __DIR__ ) ) . '/woocommerce/woocommerce.php' ) ) {
			$dir = dirname( dirname( __DIR__ ) ) . '/woocommerce';
			echo "Found WooCommerce plugin in relative dir " . dirname( dirname( __DIR__ ) ) . '/woocommerce' . PHP_EOL;
		} elseif ( file_exists( dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/woocommerce/woocommerce.php' ) ) {
			$dir = dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/woocommerce';
			echo "Found WooCommerce plugin in relative dir " . dirname( dirname( dirname( dirname( __FILE__ ) ) ) ) . '/woocommerce' . PHP_EOL;
		} elseif ( file_exists( '/tmp/wordpress/wp-content/plugins/woocommerce/woocommerce.php' ) ) {
			$dir = '/tmp/wordpress/wp-content/plugins/woocommerce';
			echo "Found WooCommerce plugin in tmp dir /tmp/wordpress/wp-content/plugins/woocommerce" . PHP_EOL;
		} elseif ( file_exists( '/home/travis/build/woocommerce/woocommerce/woocommerce.php' ) ) {
			$dir = '/home/travis/build/woocommerce/woocommerce';
			echo "Found WooCommerce plugin in home dir /home/travis/build/woocommerce/woocommerce" . PHP_EOL;
		} elseif ( file_exists( '/tmp/wordpress/wp-content/plugins/home/travis/build/woocommerce/woocommerce/woocommerce.php' ) ) {
			$dir = '/tmp/wordpress/wp-content/plugins/home/travis/build/woocommerce/woocommerce';
			echo "Found WooCommerce plugin in tmp dir /tmp/wordpress/wp-content/plugins/home/travis/build/woocommerce/woocommerce" . PHP_EOL;
		} else {
			echo "Could not find WooCommerce plugin." . PHP_EOL;
			exit( 1 );
		}
	}
	return $dir;
}


/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
	if ( ! defined( 'WOOPS_TRAVIS' ) ) { 
		define( 'WOOPS_TRAVIS', true );
	}

	// required woocommerce first
	define( 'WC_TAX_ROUNDING_MODE', 'auto' );
	define( 'WC_USE_TRANSACTIONS', false );

	echo esc_html( 'Loading WooCommerce plugin' . PHP_EOL );
	require_once wc_dir() . '/woocommerce.php';

	echo esc_html( 'Loading Predictive Search plugin' . PHP_EOL );
	require dirname( dirname( __FILE__ ) ) . '/wc-predictive-search.php';
	update_option('wc_predictive_search_lite_version', WOOPS_VERSION);
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

function _manual_install_data() {
	if ( ! defined( 'WOOPS_TRAVIS' ) ) { 
		define( 'WOOPS_TRAVIS', true );
	}

	echo esc_html( 'Installing My Plugin Data ...' . PHP_EOL );
	global $wc_predictive_search;
	$wc_predictive_search->install_databases();

	echo esc_html( 'Installing WooCommerce Data ...' . PHP_EOL );
	define( 'WP_UNINSTALL_PLUGIN', true );
	define( 'WC_REMOVE_ALL_DATA', true );
	include wc_dir() . '/uninstall.php';

	\WC_Install::install();

	// Reload capabilities after install, see https://core.trac.wordpress.org/ticket/28374.
	$GLOBALS['wp_roles'] = null; // WPCS: override ok.
	wp_roles();
}
tests_add_filter( 'setup_theme', '_manual_install_data' );

// Start up the WP testing environment.
require $_tests_dir . '/includes/bootstrap.php';

wc_test_includes();

