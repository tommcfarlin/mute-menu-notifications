<?php
/**
 * Plugin coordinator.
 *
 * @package TomMcFarlin\MMN
 */

namespace TomMcFarlin\MMN;

defined( 'ABSPATH' ) || exit;

use TomMcFarlin\MMN\Ajax\ToggleHandler;

/**
 * Central class that wires all plugin components together.
 */
class Plugin {

	/**
	 * Initialize the plugin and register all hooks.
	 *
	 * @return void
	 */
	public static function init() {
		if ( false !== get_option( 'tm_mute_menu_notifications' ) ) {
			delete_option( 'tm_mute_menu_notifications' );
		}

		$muter = new NotificationMuter();
		$muter->register();

		$admin_bar = new AdminBar( $muter );
		$admin_bar->register();

		$toggle_handler = new ToggleHandler( $muter );
		$toggle_handler->register();
	}
}
