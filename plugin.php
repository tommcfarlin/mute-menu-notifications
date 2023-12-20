<?php

/**
 * Mute Menu Notifications
 *
 * Allows you to mute the notifications in the WordPress admin menu.
 *
 * PHP version 7.4.27
 *
 * @category WordPress_Plugin
 * @package  TmMuteMenuNotifications
 * @author   Tom McFarlin <tom@tommcfarlin.com>
 * @license  GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 * @link     https://github.com/tommcfarlin/tm-mute-menu-notifications/
 * @since    2023 November 17
 *
 * @wordpress-plugin
 * Plugin Name: Mute Menu Notifications
 * Plugin URI:  https://github.com/tommcfarlin/tm-mute-menu-notifications/
 * Description: Allows you to mute the notifications in the WordPress admin menu.
 * Author:      Tom McFarlin <tom@tommcfarlin.com>
 * Version:     1.0.0
 */

namespace TmMuteMenuNotifications;

defined('WPINC') || die;
require_once __DIR__ . '/vendor/autoload.php';

register_deactivation_hook(__FILE__, function () {
    delete_option('tm_mute_menu_notifications');
});

add_action('admin_enqueue_scripts', function () {
    wp_enqueue_style(
        'tm-mute-menu-notifications',
        plugins_url('assets/css/tm-mute-menu-notifications.css', __FILE__),
        [],
        '1.0.0',
        'all'
    );

    wp_enqueue_script(
        'tm-mute-menu-notifications',
        plugins_url('assets/js/tm-mute-menu-notifications.js', __FILE__),
        [],
        strtotime('now'),
        false
    );

    wp_localize_script(
        'tm-mute-menu-notifications',
        'TmMuteMenuNotifications',
        [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tm_mute_menu_notifications_nonce'),
        ]
    );
});

add_action('admin_bar_menu', function ($wpAdminBar) {
    $wpAdminBar->add_node([
        'id' => 'tm-admin-bar-mute-menu-notifications',
        'title' => 'Mute Menu Notifications',
        'href' => '#',
        'meta' => [
            'class' => 'tm-admin-bar-mute-menu-notifications',
            'onclick' => 'return false;'
        ]
    ]);
}, 999);

add_action('wp_ajax_tm_mute_menu_notifications', function () {

    // Validate the security nonce.
    if (!wp_verify_nonce(filter_input(INPUT_GET, 'security'), 'tm_mute_menu_notifications_nonce')) {
        wp_send_json_error('Invalid nonce.');
    }

    $optionValue = get_option('tm_mute_menu_notifications');
    $newOptionValue = $optionValue ? false : true;
    wp_send_json_success(
        update_option('tm_mute_menu_notifications', $newOptionValue)
    );
});

add_action('wp_ajax_tm_get_menu_notifications', function () {
    if (!wp_verify_nonce(filter_input(INPUT_GET, 'security'), 'tm_mute_menu_notifications_nonce')) {
        wp_send_json_error('Invalid nonce.');
    }

    wp_send_json(get_option('tm_mute_menu_notifications', false));
});
