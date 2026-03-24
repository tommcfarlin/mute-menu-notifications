<?php
/**
 * Admin bar toggle button.
 *
 * @package TomMcFarlin\MMN
 */

namespace TomMcFarlin\MMN;

defined( 'ABSPATH' ) || exit;

/**
 * Adds a state-aware mute/unmute button to the WordPress admin bar.
 */
class AdminBar {

	/**
	 * The notification muter instance.
	 *
	 * @var NotificationMuter
	 */
	private $muter;

	/**
	 * Cached capability check result.
	 *
	 * @var bool|null
	 */
	private $can_manage = null;

	/**
	 * Constructor.
	 *
	 * @param NotificationMuter $muter The notification muter instance.
	 */
	public function __construct( NotificationMuter $muter ) {
		$this->muter = $muter;
	}

	/**
	 * Register hooks.
	 *
	 * @return void
	 */
	public function register() {
		add_action( 'admin_bar_menu', array( $this, 'add_toggle_node' ), 999 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_assets' ) );
	}

	/**
	 * Check if current user can manage plugin updates (cached).
	 *
	 * @return bool
	 */
	private function user_can_manage() {
		if ( null === $this->can_manage ) {
			$this->can_manage = current_user_can( 'update_plugins' );
		}
		return $this->can_manage;
	}

	/**
	 * Add the mute/unmute toggle node to the admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The admin bar instance.
	 *
	 * @return void
	 */
	public function add_toggle_node( $wp_admin_bar ) {
		if ( ! is_admin() || ! $this->user_can_manage() ) {
			return;
		}

		$is_muted = $this->muter->is_muted();
		$label    = $is_muted
			? __( 'Unmute Notifications', 'mute-menu-notifications' )
			: __( 'Mute Notifications', 'mute-menu-notifications' );
		$icon     = $is_muted ? 'dashicons-hidden' : 'dashicons-bell';

		$wp_admin_bar->add_node(
			array(
				'id'    => 'mutemenu-toggle',
				'title' => '<span class="ab-icon dashicons ' . esc_attr( $icon ) . '"></span>'
					. '<span class="ab-label">' . esc_html( $label ) . '</span>',
				'href'  => '#',
				'meta'  => array(
					'class' => 'mutemenu-toggle',
					'title' => esc_attr( $label ),
				),
			)
		);
	}

	/**
	 * Enqueue JavaScript and CSS for the admin bar toggle.
	 *
	 * @return void
	 */
	public function enqueue_assets() {
		if ( ! $this->user_can_manage() ) {
			return;
		}

		wp_enqueue_style(
			'mutemenu',
			plugins_url( 'assets/css/mutemenu.css', MUTEMENU_PLUGIN_FILE ),
			array(),
			MUTEMENU_VERSION,
			'all'
		);

		wp_enqueue_script(
			'mutemenu',
			plugins_url( 'assets/js/mutemenu.js', MUTEMENU_PLUGIN_FILE ),
			array(),
			MUTEMENU_VERSION,
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script(
			'mutemenu',
			'MuteMenu',
			array(
				'ajaxUrl'        => admin_url( 'admin-ajax.php' ),
				'nonce'          => wp_create_nonce( 'mutemenu_toggle' ),
				'isMuted'        => $this->muter->is_muted() ? '1' : '',
				'labelMute'      => __( 'Mute Notifications', 'mute-menu-notifications' ),
				'labelUnmute'    => __( 'Unmute Notifications', 'mute-menu-notifications' ),
				'confirmMuted'   => __( 'Notifications muted.', 'mute-menu-notifications' ),
				'confirmUnmuted' => __( 'Notifications unmuted.', 'mute-menu-notifications' ),
				'errorMessage'   => __( 'Could not update notification preference. Please try again.', 'mute-menu-notifications' ),
			)
		);
	}
}
