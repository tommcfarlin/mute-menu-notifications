# Mute Menu Notifications

![WordPress Plugin Version](https://img.shields.io/badge/version-2.0.0-blue)
![WordPress Tested](https://img.shields.io/badge/wordpress-6.9-green)
![PHP Version](https://img.shields.io/badge/php-%3E%3D7.4-purple)
![License](https://img.shields.io/badge/license-GPL--3.0-orange)

Allows you to mute the update notification badges in the WordPress admin menu.

## Description

Mute Menu Notifications adds a toggle to the WordPress admin bar that lets administrators hide or show the update notification badges throughout the admin menu.

When muted, the red update count bubbles on menu items like Plugins, Themes, and Updates are hidden. On the Plugins page, the individual plugin update rows are also hidden.

The preference is stored per-user and persists across page loads. Only users with the `update_plugins` capability (Administrators by default) can see and use the toggle. This capability was chosen because the notification badges this plugin hides (plugin updates, theme updates, core updates) are only visible to users who have that capability.

## Requirements

- PHP 7.4 or later
- WordPress 6.9 or later

## Installation

### Using the WordPress Dashboard

1. Navigate to the "Add New" Plugin Dashboard.
2. Select `mute-menu-notifications.zip` from your computer.
3. Upload.
4. Activate the plugin on the WordPress Plugin Dashboard.

### Using FTP

1. Extract `mute-menu-notifications.zip` to your computer.
2. Upload the `mute-menu-notifications` directory to your `wp-content/plugins` directory.
3. Activate the plugin on the WordPress Plugins Dashboard.

### Git

1. Navigate to the `plugins` directory of your WordPress installation.
2. Run `git clone git@github.com:tommcfarlin/mute-menu-notifications.git`

## Development

```bash
# Install dependencies
composer install

# Run tests
composer test

# Check coding standards
composer lint

# Auto-fix coding standards
composer lint:fix
```

## License

GPL-3.0 -- see [LICENSE](LICENSE) for details.
