# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Changed
- Refactored plugin into OOP architecture with PSR-4 autoloading under `TomMcFarlin\MMN` namespace
- Notifications are now hidden server-side via inline CSS in `admin_head`, eliminating flicker on page load
- Toggle button uses POST instead of GET for the AJAX request
- Admin bar button now shows state-aware label ("Mute Notifications" / "Unmute Notifications") with dashicon
- Hidden elements use `display: none` instead of `visibility: hidden` to eliminate layout gaps
- JavaScript loads in the footer and is browser-cacheable with a static version string
- Removed the on-page-load AJAX call; mute state is passed to JS via `wp_localize_script`

### Added
- Capability check (`update_plugins`) on AJAX handler and admin bar button
- Nonce verification via `check_ajax_referer()`
- `uninstall.php` for proper option cleanup on plugin deletion
- Unit tests with PHPUnit and Brain Monkey for all classes
- WPCS compliance via `phpcs.xml.dist`
- `readme.txt` for WordPress.org format
- Contributing guide, release checklist, and GitHub issue/PR templates

### Removed
- Deactivation hook (replaced by `uninstall.php`)
- Second AJAX endpoint (`tm_get_menu_notifications`) no longer needed
- Dead `URLSearchParams` code in JavaScript

### Security
- All AJAX handlers now verify `current_user_can( 'update_plugins' )`
- Admin bar toggle and assets only render for authorized users

## [1.0.0] - 2023-10-26

### Added
- Initial release
