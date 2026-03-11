<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package TomMcFarlin\MMN
 */

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Mockery\Adapter\Phpunit\TestListener;
use Brain\Monkey;

/**
 * Base test case for all plugin tests.
 */
class MMN_TestCase extends \PHPUnit\Framework\TestCase {

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
