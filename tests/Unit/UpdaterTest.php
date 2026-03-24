<?php
/**
 * Tests for Updater.
 *
 * @package TomMcFarlin\MMN\Tests
 */

namespace TomMcFarlin\MMN\Tests\Unit;

use TomMcFarlin\MMN\Updater;
use Brain\Monkey\Functions;

class UpdaterTest extends \MuteMenu_TestCase {

	/**
	 * @test
	 */
	public function register_hooks_update_filter_and_source_selection() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		Functions\expect( 'add_filter' )
			->once()
			->with( 'update_plugins_github.com', \Mockery::type( 'array' ), 10, 4 );

		Functions\expect( 'add_filter' )
			->once()
			->with( 'upgrader_source_selection', \Mockery::type( 'array' ), 10, 4 );

		$updater->register();

		$this->addToAssertionCount( 1 );
	}

	/**
	 * @test
	 */
	public function check_for_update_skips_other_plugins() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		$result = $updater->check_for_update( false, array(), 'other-plugin/other.php', array() );

		$this->assertFalse( $result );
	}

	/**
	 * @test
	 */
	public function check_for_update_returns_false_when_no_release() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'mutemenu_github_release' )
			->andReturn( false );

		Functions\expect( 'wp_remote_get' )
			->once()
			->andReturn( new \WP_Error( 'http_request_failed', 'Connection error' ) );

		Functions\expect( 'is_wp_error' )
			->once()
			->andReturn( true );

		$result = $updater->check_for_update(
			false,
			array(),
			'mute-menu-notifications/mute-menu-notifications.php',
			array()
		);

		$this->assertFalse( $result );
	}

	/**
	 * @test
	 */
	public function check_for_update_returns_false_when_same_version() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'mutemenu_github_release' )
			->andReturn(
				array(
					'tag_name'    => 'v2.0.0',
					'html_url'    => 'https://github.com/tommcfarlin/mute-menu-notifications/releases/tag/v2.0.0',
					'zipball_url' => 'https://api.github.com/repos/tommcfarlin/mute-menu-notifications/zipball/v2.0.0',
				)
			);

		$result = $updater->check_for_update(
			false,
			array(),
			'mute-menu-notifications/mute-menu-notifications.php',
			array()
		);

		$this->assertFalse( $result );
	}

	/**
	 * @test
	 */
	public function check_for_update_returns_update_data_when_newer() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		Functions\expect( 'get_transient' )
			->once()
			->with( 'mutemenu_github_release' )
			->andReturn(
				array(
					'tag_name'    => 'v2.1.0',
					'html_url'    => 'https://github.com/tommcfarlin/mute-menu-notifications/releases/tag/v2.1.0',
					'zipball_url' => 'https://api.github.com/repos/tommcfarlin/mute-menu-notifications/zipball/v2.1.0',
				)
			);

		$plugin_data = array(
			'UpdateURI' => 'https://github.com/tommcfarlin/mute-menu-notifications/',
		);

		$result = $updater->check_for_update(
			false,
			$plugin_data,
			'mute-menu-notifications/mute-menu-notifications.php',
			array()
		);

		$this->assertIsArray( $result );
		$this->assertSame( '2.1.0', $result['version'] );
		$this->assertSame( 'mute-menu-notifications', $result['slug'] );
		$this->assertSame( $plugin_data['UpdateURI'], $result['id'] );
		$this->assertArrayHasKey( 'package', $result );
		$this->assertArrayHasKey( 'url', $result );
	}

	/**
	 * @test
	 */
	public function fix_source_directory_skips_other_plugins() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		$source = '/tmp/source/other-plugin/';
		$result = $updater->fix_source_directory(
			$source,
			'/tmp/source',
			null,
			array( 'plugin' => 'other-plugin/other.php' )
		);

		$this->assertSame( $source, $result );
	}

	/**
	 * @test
	 */
	public function fix_source_directory_skips_when_no_plugin_context() {
		Functions\expect( 'plugin_basename' )
			->once()
			->andReturn( 'mute-menu-notifications/mute-menu-notifications.php' );

		$updater = new Updater( '/path/to/mute-menu-notifications.php', '2.0.0' );

		$source = '/tmp/source/some-dir/';
		$result = $updater->fix_source_directory(
			$source,
			'/tmp/source',
			null,
			array()
		);

		$this->assertSame( $source, $result );
	}
}
