<?php
/**
 * Tests for AdminBar.
 *
 * @package TomMcFarlin\MMN\Tests
 */

namespace TomMcFarlin\MMN\Tests\Unit;

use TomMcFarlin\MMN\AdminBar;
use TomMcFarlin\MMN\NotificationMuter;
use Brain\Monkey\Functions;
use Mockery;

class AdminBarTest extends \MMN_TestCase {

	/**
	 * @test
	 */
	public function register_hooks_admin_bar_menu_and_enqueue_scripts() {
		$muter     = Mockery::mock( NotificationMuter::class );
		$admin_bar = new AdminBar( $muter );

		Functions\expect( 'add_action' )
			->once()
			->with( 'admin_bar_menu', array( $admin_bar, 'add_toggle_node' ), 999 );

		Functions\expect( 'add_action' )
			->once()
			->with( 'admin_enqueue_scripts', array( $admin_bar, 'enqueue_assets' ) );

		$admin_bar->register();

		$this->assertTrue( true );
	}

	/**
	 * @test
	 */
	public function add_toggle_node_skips_for_unauthorized_users() {
		$muter     = Mockery::mock( NotificationMuter::class );
		$admin_bar = new AdminBar( $muter );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( false );

		$wp_admin_bar = Mockery::mock( 'WP_Admin_Bar' );
		$wp_admin_bar->shouldNotReceive( 'add_node' );

		$admin_bar->add_toggle_node( $wp_admin_bar );

		$this->assertTrue( true );
	}

	/**
	 * @test
	 */
	public function add_toggle_node_shows_mute_label_when_not_muted() {
		$muter = Mockery::mock( NotificationMuter::class );
		$muter->shouldReceive( 'is_muted' )->andReturn( false );

		$admin_bar = new AdminBar( $muter );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( true );

		Functions\expect( '__' )
			->andReturnFirstArg();

		Functions\expect( 'esc_attr' )
			->andReturnFirstArg();

		Functions\expect( 'esc_html' )
			->andReturnFirstArg();

		$wp_admin_bar = Mockery::mock( 'WP_Admin_Bar' );
		$wp_admin_bar->shouldReceive( 'add_node' )
			->once()
			->with(
				Mockery::on(
					function ( $args ) {
						return 'tm-mmn-toggle' === $args['id']
							&& false !== strpos( $args['title'], 'dashicons-bell' )
							&& false !== strpos( $args['title'], 'Mute Notifications' );
					}
				)
			);

		$admin_bar->add_toggle_node( $wp_admin_bar );

		$this->assertTrue( true );
	}

	/**
	 * @test
	 */
	public function add_toggle_node_shows_unmute_label_when_muted() {
		$muter = Mockery::mock( NotificationMuter::class );
		$muter->shouldReceive( 'is_muted' )->andReturn( true );

		$admin_bar = new AdminBar( $muter );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( true );

		Functions\expect( '__' )
			->andReturnFirstArg();

		Functions\expect( 'esc_attr' )
			->andReturnFirstArg();

		Functions\expect( 'esc_html' )
			->andReturnFirstArg();

		$wp_admin_bar = Mockery::mock( 'WP_Admin_Bar' );
		$wp_admin_bar->shouldReceive( 'add_node' )
			->once()
			->with(
				Mockery::on(
					function ( $args ) {
						return 'tm-mmn-toggle' === $args['id']
							&& false !== strpos( $args['title'], 'dashicons-hidden' )
							&& false !== strpos( $args['title'], 'Unmute Notifications' );
					}
				)
			);

		$admin_bar->add_toggle_node( $wp_admin_bar );

		$this->assertTrue( true );
	}

	/**
	 * @test
	 */
	public function enqueue_assets_skips_for_unauthorized_users() {
		$muter     = Mockery::mock( NotificationMuter::class );
		$admin_bar = new AdminBar( $muter );

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( false );

		Functions\expect( 'wp_enqueue_style' )->never();
		Functions\expect( 'wp_enqueue_script' )->never();

		$admin_bar->enqueue_assets();

		$this->assertTrue( true );
	}

	/**
	 * @test
	 */
	public function enqueue_assets_registers_style_script_and_localized_data() {
		$muter = Mockery::mock( NotificationMuter::class );
		$muter->shouldReceive( 'is_muted' )->andReturn( false );

		$admin_bar = new AdminBar( $muter );

		if ( ! defined( 'TMM_PLUGIN_FILE' ) ) {
			define( 'TMM_PLUGIN_FILE', '/path/to/plugin.php' );
		}

		if ( ! defined( 'TMM_VERSION' ) ) {
			define( 'TMM_VERSION', '2.0.0' );
		}

		Functions\expect( 'current_user_can' )
			->once()
			->with( 'update_plugins' )
			->andReturn( true );

		Functions\expect( 'plugins_url' )
			->twice()
			->andReturn( 'http://example.com/assets/css/tm-mute-menu-notifications.css' );

		Functions\expect( 'admin_url' )
			->once()
			->with( 'admin-ajax.php' )
			->andReturn( 'http://example.com/wp-admin/admin-ajax.php' );

		Functions\expect( 'wp_create_nonce' )
			->once()
			->with( 'tm_mmn_toggle' )
			->andReturn( 'test-nonce' );

		Functions\expect( 'wp_enqueue_style' )->once();
		Functions\expect( 'wp_enqueue_script' )->once();
		Functions\expect( 'wp_localize_script' )
			->once()
			->with(
				'tm-mute-menu-notifications',
				'TmMuteMenuNotifications',
				Mockery::on(
					function ( $data ) {
						return isset( $data['ajaxUrl'] )
							&& isset( $data['nonce'] )
							&& isset( $data['muted'] )
							&& false === $data['muted'];
					}
				)
			);

		$admin_bar->enqueue_assets();

		$this->assertTrue( true );
	}
}
