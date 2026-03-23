=== Mute Menu Notifications ===
Contributors: tommcfarlin
Donate link: https://buymeacoffee.com/tommcfarlin
Tags: admin, notifications, menu, updates, mute
Requires at least: 6.9
Tested up to: 6.9.1
Requires PHP: 7.4
Stable tag: 2.0.0
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html

Allows you to mute the update notification badges in the WordPress admin menu.

== Description ==

Mute Menu Notifications adds a toggle to the WordPress admin bar that lets administrators hide or show the update notification badges throughout the admin menu.

When muted, the red update count bubbles on menu items like Plugins, Themes, and Updates are hidden. On the Plugins page, the individual plugin update rows are also hidden.

The preference is stored per-user and persists across page loads. Only users with the `update_plugins` capability (Administrators by default) can see and use the toggle. This capability was chosen because the notification badges this plugin hides (plugin updates, theme updates, core updates) are only visible to users who have that capability.

== Installation ==

1. Upload the `tm-mute-menu-notifications` directory to `/wp-content/plugins/`.
2. Activate the plugin through the Plugins menu in WordPress.
3. Click "Mute Notifications" in the admin bar to toggle notification visibility.

== Changelog ==

= 2.0.0 =
* Refactored into OOP architecture with flicker-free server-side muting.
* Mute preference is now per-user instead of site-wide.
* Added ARIA attributes on toggle for screen reader accessibility.
* Added translatable toggle labels in JavaScript.
* Added visual error feedback and icon transition on toggle.
* Added capability checks, nonce verification, and unit tests.
* One-time cleanup of legacy option on upgrade from 1.x.
* See CHANGELOG.md for full details.

= 1.0.0 =
* Initial release.
