<?php
/**
 * Core mute logic and inline CSS injection.
 *
 * @package TomMcFarlin\MMN
 */

namespace TomMcFarlin\MMN;

defined( 'ABSPATH' ) || exit;

/**
 * Handles reading, toggling, and rendering the muted state.
 */
class NotificationMuter {

	/**
	 * User meta key stored in wp_usermeta.
	 *
	 * @var string
	 */
	const META_KEY = 'mutemenu_muted';

	/**
	 * Cached mute state for the current request.
	 *
	 * @var bool|null
	 */
	private $muted = null;

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_head', array( $this, 'inject_inline_css' ) );
	}

	/**
	 * Whether notifications are currently muted.
	 *
	 * @return bool
	 */
	public function is_muted() {
		if ( null === $this->muted ) {
			$this->muted = '1' === get_user_meta( get_current_user_id(), self::META_KEY, true );
		}

		return $this->muted;
	}

	/**
	 * Toggle the mute state and return the new value.
	 *
	 * @return bool The new muted state.
	 */
	public function toggle() {
		$new_state = ! $this->is_muted();

		update_user_meta( get_current_user_id(), self::META_KEY, $new_state ? '1' : '0' );
		$this->muted = $new_state;

		return $new_state;
	}

	/**
	 * Inject inline CSS to hide notifications when muted.
	 *
	 * Fires on admin_head so the styles are in the <head> before
	 * the body renders, preventing any flicker.
	 *
	 * @return void
	 */
	public function inject_inline_css() {
		if ( ! $this->is_muted() ) {
			return;
		}

		echo '<style id="mutemenu-hide">'
			. '.update-plugins { display: none !important; }'
			. '#wp-admin-bar-updates { display: none !important; }'
			. '.plugin-update-tr { display: none !important; }'
			. '</style>';
	}
}
