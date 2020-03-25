<?php
/**
 * Class SampleTest
 *
 * @package Sample_Plugin
 */

/**
 * Sample test case.
 */
class a3Rev_Tests_PS_Sync_Database extends WP_UnitTestCase {

	function test_sync_products() {

		// First create simple product 
		WC_Helper_Product::create_simple_product();

		global $wc_ps_sync;
		$result = $wc_ps_sync->wc_predictive_search_sync_posts( 'product', 'auto_sync', 'auto' );

		$status = '';
		if ( isset( $result['status'] ) ) {
			$status = $result['status'];
		}

		$this->assertEquals( 'completed', $status );
	}
}
