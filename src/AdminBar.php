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
	 * Add the mute/unmute toggle node to the admin bar.
	 *
	 * @param \WP_Admin_Bar $wp_admin_bar The admin bar instance.
	 *
	 * @return void
	 */
	public function add_toggle_node( $wp_admin_bar ) {
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		$is_muted = $this->muter->is_muted();
		$label    = $is_muted
			? __( 'Unmute Notifications', 'tm-mute-menu-notifications' )
			: __( 'Mute Notifications', 'tm-mute-menu-notifications' );
		$icon     = $is_muted ? 'dashicons-hidden' : 'dashicons-bell';

		$wp_admin_bar->add_node(
			array(
				'id'    => 'tm-mmn-toggle',
				'title' => '<span class="ab-icon dashicons ' . esc_attr( $icon ) . '"></span>'
					. '<span class="ab-label">' . esc_html( $label ) . '</span>',
				'href'  => '#',
				'meta'  => array(
					'class'        => 'tm-mmn-toggle',
					'aria-label'   => $label,
					'role'         => 'button',
					'aria-pressed' => $is_muted ? 'true' : 'false',
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
		if ( ! current_user_can( 'update_plugins' ) ) {
			return;
		}

		wp_enqueue_style(
			'tm-mute-menu-notifications',
			plugins_url( 'assets/css/tm-mute-menu-notifications.css', TMM_PLUGIN_FILE ),
			array(),
			TMM_VERSION,
			'all'
		);

		wp_enqueue_script(
			'tm-mute-menu-notifications',
			plugins_url( 'assets/js/tm-mute-menu-notifications.js', TMM_PLUGIN_FILE ),
			array(),
			TMM_VERSION,
			true
		);

		wp_localize_script(
			'tm-mute-menu-notifications',
			'TmMuteMenuNotifications',
			array(
				'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
				'nonce'       => wp_create_nonce( 'tm_mmn_toggle' ),
				'muted'       => $this->muter->is_muted(),
				'labelMute'   => __( 'Mute Notifications', 'tm-mute-menu-notifications' ),
				'labelUnmute' => __( 'Unmute Notifications', 'tm-mute-menu-notifications' ),
			)
		);
	}
}
