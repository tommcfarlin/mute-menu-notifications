<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package TomMcFarlin\MMN
 */

define( 'ABSPATH', dirname( __DIR__ ) . '/' );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Brain\Monkey;

/**
 * Base test case for all plugin tests.
 */
class MuteMenu_TestCase extends \PHPUnit\Framework\TestCase {

	/**
	 * Set up Brain Monkey before each test.
	 */
	protected function setUp(): void {
		parent::setUp();
		Monkey\setUp();
	}

	/**
	 * Tear down Brain Monkey after each test.
	 */
	protected function tearDown(): void {
		Monkey\tearDown();
		Mockery::close();
		parent::tearDown();
	}
}
