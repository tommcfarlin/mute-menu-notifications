<?php
/**
 * Tests for Ajax\ToggleHandler.
 *
 * @package TomMcFarlin\MMN\Tests
 */

namespace TomMcFarlin\MMN\Tests\Unit\Ajax;

use TomMcFarlin\MMN\Ajax\ToggleHandler;
use TomMcFarlin\MMN\NotificationMuter;
use Brain\Monkey\Functions;
use Mockery;

class ToggleHandlerTest extends \MuteMenu_TestCase {

	/**
	 * @test
	 */
	public function register_hooks_wp_ajax_action() {
		$muter   = Mockery::mock( NotificationMuter::class );
		$handler = new ToggleHandler( $muter );

		Functions\expect( 'add_action' )
			->once()
			->with( 'wp_ajax_mutemenu_toggle', array( $handler, 'handle' ) );

		$handler->register();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * @test
	 */
	public function handle_dies_on_invalid_nonce() {
		$muter = Mockery::mock( NotificationMuter::class );
		$muter->shouldNotReceive( 'toggle' );

		$handler = new ToggleHandler( $muter );

		Functions\expect( 'check_ajax_referer' )
			->once()
			->with( 'mutemenu_toggle', 'nonce' )
			->andReturnUsing(
				function () {
					throw new \RuntimeException( 'wp_die called' );
				}
			);

		$this->expectException( \RuntimeException::class );

		$handler->handle();
	}

	/**
	 * @test
	 */
	public function handle_sends_error_when_user_lacks_capability() {
		$muter = Mockery::mock( NotificationMuter::class );
		$muter->shouldNotReceive( 'toggle' );

		$handler = new ToggleHandler( $muter );

		Functions\expect( 'check_ajax_referer' )
			->once()
			->with( 'mutemenu_toggle', 'nonce' );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( false );

		Functions\expect( '__' )
			->andReturnFirstArg();

		$error_called = false;

		Functions\expect( 'wp_send_json_error' )
			->once()
			->with( 'You do not have permission to perform this action.', 403 )
			->andReturnUsing(
				function () use ( &$error_called ) {
					$error_called = true;
					throw new \RuntimeException( 'wp_send_json_error called' );
				}
			);

		try {
			$handler->handle();
		} catch ( \RuntimeException $e ) {
			// Expected termination.
		}

		$this->assertTrue( $error_called );
	}

	/**
	 * @test
	 */
	public function handle_toggles_and_returns_new_state() {
		$muter = Mockery::mock( NotificationMuter::class );
		$muter->shouldReceive( 'toggle' )
			->once()
			->andReturn( true );

		$handler = new ToggleHandler( $muter );

		Functions\expect( 'check_ajax_referer' )
			->once()
			->with( 'mutemenu_toggle', 'nonce' );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( true );

		$success_data = null;

		Functions\expect( 'wp_send_json_success' )
			->once()
			->with( array( 'muted' => true ) )
			->andReturnUsing(
				function ( $data ) use ( &$success_data ) {
					$success_data = $data;
				}
			);

		$handler->handle();

		$this->assertSame( array( 'muted' => true ), $success_data );
	}
}
