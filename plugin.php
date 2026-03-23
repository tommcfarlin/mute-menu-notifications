<?php
/**
 * Mute Menu Notifications
 *
 * @package   TomMcFarlin\MMN
 * @author    Tom McFarlin <tom@tommcfarlin.com>
 * @license   GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 * @link      https://github.com/tommcfarlin/mute-menu-notifications/
 *
 * @wordpress-plugin
 * Plugin Name: Mute Menu Notifications
 * Plugin URI:  https://github.com/tommcfarlin/mute-menu-notifications/
 * Description: Allows you to mute the update notification badges in the WordPress admin menu.
 * Author:      Tom McFarlin <tom@tommcfarlin.com>
 * Author URI:  https://tommcfarlin.com
 * Version:     2.0.0
 * Requires at least: 6.9
 * Requires PHP: 7.4
 * License:     GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
 * Text Domain: mute-menu-notifications
 */

defined( 'WPINC' ) || die;

define( 'MUTEMENU_VERSION', '2.0.0' );
define( 'MUTEMENU_PLUGIN_FILE', __FILE__ );
define( 'MUTEMENU_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

require_once MUTEMENU_PLUGIN_DIR . 'vendor/autoload.php';

TomMcFarlin\MMN\Plugin::init();
