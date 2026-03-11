<?php
/**
 * Tests for Plugin.
 *
 * @package TomMcFarlin\MMN\Tests
 */

namespace TomMcFarlin\MMN\Tests\Unit;

use TomMcFarlin\MMN\Plugin;
use Brain\Monkey\Functions;
use Brain\Monkey\Actions;

class PluginTest extends \MMN_TestCase {

	/**
	 * @test
	 */
	public function init_registers_all_expected_hooks() {
		Functions\expect( 'add_action' )
			->with( 'admin_head', \Mockery::type( 'array' ) )
			->once();

		Functions\expect( 'add_action' )
			->with( 'admin_bar_menu', \Mockery::type( 'array' ), 999 )
			->once();

		Functions\expect( 'add_action' )
			->with( 'admin_enqueue_scripts', \Mockery::type( 'array' ) )
			->once();

		Functions\expect( 'add_action' )
			->with( 'wp_ajax_tm_mmn_toggle', \Mockery::type( 'array' ) )
			->once();

		Plugin::init();

		$this->assertTrue( true );
	}
}
