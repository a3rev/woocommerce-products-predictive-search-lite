<?php
/**
 * Class SampleTest
 *
 * @package Sample_Plugin
 */

/**
 * Sample test case.
 */
class a3Rev_Tests_PS extends WP_UnitTestCase {

	function test_constants_defined() {
		$this->assertTrue( defined( 'WOOPS_KEY' ) );
		$this->assertTrue( defined( 'WOOPS_PREFIX' ) );
		$this->assertTrue( defined( 'WOOPS_VERSION' ) );
	}
}
