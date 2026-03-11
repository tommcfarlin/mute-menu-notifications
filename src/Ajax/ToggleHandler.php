<?php
/**
 * AJAX handler for toggling the mute state.
 *
 * @package TomMcFarlin\MMN
 */

namespace TomMcFarlin\MMN\Ajax;

defined( 'ABSPATH' ) || exit;

use TomMcFarlin\MMN\NotificationMuter;

/**
 * Handles the AJAX POST request to toggle notification muting.
 */
class ToggleHandler {

	/**
	 * The notification muter instance.
	 *
	 * @var NotificationMuter
	 */
	private $muter;

	/**
	 * Constructor.
	 *
	 * @param NotificationMuter $muter The notification muter instance.
	 */
	public function __construct( NotificationMuter $muter ) {
		$this->muter = $muter;
	}

	/**
	 * Register the AJAX action.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'wp_ajax_tm_mmn_toggle', array( $this, 'handle' ) );
	}

	/**
	 * Handle the toggle request.
	 *
	 * @return void
	 */
	public function handle() {
		check_ajax_referer( 'tm_mmn_toggle', 'nonce' );

		if ( ! current_user_can( 'update_plugins' ) ) {
			wp_send_json_error(
				__( 'You do not have permission to perform this action.', 'tm-mute-menu-notifications' ),
				403
			);
		}

		$new_state = $this->muter->toggle();

		wp_send_json_success( array( 'muted' => $new_state ) );
	}
}
