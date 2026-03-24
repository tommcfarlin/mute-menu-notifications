<?php
/**
 * PHPUnit bootstrap file.
 *
 * @package TomMcFarlin\MMN
 */

define( 'ABSPATH', dirname( __DIR__ ) . '/' );

require_once dirname( __DIR__ ) . '/vendor/autoload.php';

use Brain\Monkey;

if ( ! class_exists( 'WP_Error' ) ) {
	/**
	 * Minimal WP_Error stub for unit tests.
	 */
	class WP_Error {
		/**
		 * Error code.
		 *
		 * @var string
		 */
		private $code;

		/**
		 * Error message.
		 *
		 * @var string
		 */
		private $message;

		/**
		 * Constructor.
		 *
		 * @param string $code    Error code.
		 * @param string $message Error message.
		 */
		public function __construct( $code = '', $message = '' ) {
			$this->code    = $code;
			$this->message = $message;
		}
	}
}

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
