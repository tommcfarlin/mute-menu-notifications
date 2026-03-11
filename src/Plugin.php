<?php
/**
 * Plugin coordinator.
 *
 * @package TomMcFarlin\MMN
 */

namespace TomMcFarlin\MMN;

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
		$muter = new NotificationMuter();
		$muter->register();

		$admin_bar = new AdminBar( $muter );
		$admin_bar->register();

		$toggle_handler = new ToggleHandler( $muter );
		$toggle_handler->register();
	}
}
