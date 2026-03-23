<?php
/**
 * Tests for NotificationMuter.
 *
 * @package TomMcFarlin\MMN\Tests
 */

namespace TomMcFarlin\MMN\Tests\Unit;

use TomMcFarlin\MMN\NotificationMuter;
use Brain\Monkey\Functions;

class NotificationMuterTest extends \MuteMenu_TestCase {

	/**
	 * @test
	 */
	public function register_hooks_into_admin_head() {
		$muter = new NotificationMuter();

		Functions\expect( 'add_action' )
			->once()
			->with( 'admin_head', array( $muter, 'inject_inline_css' ) );

		$muter->register();

		$this->assertTrue( true );
	}

	/**
	 * @test
	 */
	public function is_muted_returns_false_by_default() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->once()
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '' );

		$this->assertFalse( $muter->is_muted() );
	}

	/**
	 * @test
	 */
	public function is_muted_returns_true_when_option_is_set() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->once()
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '1' );

		$this->assertTrue( $muter->is_muted() );
	}

	/**
	 * @test
	 */
	public function is_muted_caches_result_across_calls() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->once()
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '1' );

		$first  = $muter->is_muted();
		$second = $muter->is_muted();

		// get_user_meta should only be called once due to caching.
		$this->assertSame( $first, $second );
	}

	/**
	 * @test
	 */
	public function toggle_flips_from_false_to_true() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '' );

		Functions\expect( 'update_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', '1' );

		$result = $muter->toggle();

		$this->assertTrue( $result );
	}

	/**
	 * @test
	 */
	public function toggle_flips_from_true_to_false() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '1' );

		Functions\expect( 'update_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', '0' );

		$result = $muter->toggle();

		$this->assertFalse( $result );
	}

	/**
	 * @test
	 */
	public function inject_inline_css_outputs_nothing_when_not_muted() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->once()
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '' );

		ob_start();
		$muter->inject_inline_css();
		$output = ob_get_clean();

		$this->assertEmpty( $output );
	}

	/**
	 * @test
	 */
	public function inject_inline_css_outputs_hide_styles_when_muted() {
		$muter = new NotificationMuter();

		Functions\expect( 'get_current_user_id' )
			->once()
			->andReturn( 1 );

		Functions\expect( 'get_user_meta' )
			->once()
			->with( 1, 'mutemenu_muted', true )
			->andReturn( '1' );

		ob_start();
		$muter->inject_inline_css();
		$output = ob_get_clean();

		$this->assertStringContainsString( '<style id="mutemenu-hide">', $output );
		$this->assertStringContainsString( '.update-plugins', $output );
		$this->assertStringContainsString( '#wp-admin-bar-updates', $output );
		$this->assertStringContainsString( '.plugin-update-tr', $output );
		$this->assertStringContainsString( 'display: none', $output );
	}
}
