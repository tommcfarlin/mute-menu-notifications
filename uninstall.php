<?php
/**
 * Uninstall handler for Mute Menu Notifications.
 *
 * Fired when the plugin is deleted via the WordPress admin.
 *
 * @package TomMcFarlin\MMN
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

delete_metadata( 'user', 0, 'mutemenu_muted', '', true );
delete_option( 'tm_mute_menu_notifications' );
