# Refactoring Plan: Mute Menu Notifications

## Overview

This document outlines the complete refactoring of the Mute Menu Notifications plugin from a single-file procedural plugin into a properly architected, performant, and secure WordPress plugin.

## Goals

1. Eliminate notification flicker on page load
2. Follow WordPress Coding Standards (WPCS)
3. Proper OOP architecture with PSR-4 autoloading
4. Security hardening (capability checks, proper nonce handling)
5. Testability with Brain Monkey / PHPUnit
6. Clean UI with state feedback
7. Production-ready build and release workflow

## Current Problems

| Problem | Impact |
|---------|--------|
| Notifications flicker on load (JS hides after AJAX round-trip) | Poor UX |
| No capability check on AJAX handlers | Security (any authenticated user can toggle) |
| `strtotime('now')` as script version | Prevents browser caching |
| GET request for state-changing action | Violates HTTP semantics |
| Dead JS code (`URLSearchParams` never used) | Code quality |
| No visual feedback on toggle state | Confusing UX |
| `visibility: hidden` leaves layout gaps | Visual artifact |
| No tests | Reliability |
| Monolithic plugin.php with closures | Untestable, hard to extend |

## Architecture

### Directory Structure

```
tm-mute-menu-notifications/
  .github/
    ISSUE_TEMPLATE/
    PULL_REQUEST_TEMPLATE.md
    labels.yml
  assets/
    css/
      tm-mute-menu-notifications.css
    js/
      tm-mute-menu-notifications.js
  docs/
    REFACTORING-PLAN.md
  personas/
  scripts/
  src/
    Plugin.php
    AdminBar.php
    NotificationMuter.php
    Ajax/
      ToggleHandler.php
  tests/
    bootstrap.php
    Unit/
      PluginTest.php
      AdminBarTest.php
      NotificationMuterTest.php
      Ajax/
        ToggleHandlerTest.php
  CHANGELOG.md
  CONTRIBUTING.md
  LICENSE
  plugin.php
  README.md
  readme.txt
  RELEASE.md
  composer.json
  uninstall.php
```

### Class Responsibilities

#### `plugin.php` (Bootstrap)

- Plugin header, constants (`TMM_VERSION`, `TMM_PLUGIN_FILE`, `TMM_PLUGIN_DIR`)
- Composer autoloader require
- Instantiate `Plugin` and call `Plugin::init()`
- Deactivation hook registration

#### `src/Plugin.php`

- Central coordinator
- Registers all hooks by instantiating and wiring the other classes
- Provides `init()` static method as the single entry point
- Holds plugin version and capability constants

#### `src/AdminBar.php`

- Adds the toggle node to the admin bar via `admin_bar_menu`
- Reads current mute state to set the correct label and icon
- Only renders for users with `update_plugins` capability
- Enqueues the JS and CSS assets (only in admin, only for capable users)
- Localizes the script with AJAX URL, nonce, and current mute state

#### `src/NotificationMuter.php`

- Hooks into `admin_head` to inject inline CSS when muted
- Targets: `.update-plugins`, `#wp-admin-bar-updates .ab-label`, `.plugin-update-tr`
- Uses `display: none` instead of `visibility: hidden`
- Reads the option once per request and caches the value in a property
- Provides `is_muted(): bool` and `toggle(): bool` methods
- Handles option cleanup on deactivation

#### `src/Ajax/ToggleHandler.php`

- Registers `wp_ajax_tm_mute_menu_notifications` action
- Verifies nonce via `check_ajax_referer()`
- Checks `current_user_can( 'update_plugins' )`
- Delegates to `NotificationMuter::toggle()`
- Returns JSON with the new mute state: `{ success: true, data: { muted: bool } }`
- Uses POST method

### JavaScript (`assets/js/tm-mute-menu-notifications.js`)

- On DOMContentLoaded: read initial state from localized `TmMuteMenuNotifications.muted`
- No AJAX call on page load (state comes from PHP)
- Click handler on the admin bar button:
  - POST to toggle endpoint
  - On success, toggle a `<style>` element in the `<head>` (add/remove)
  - Update the button label and dashicon to reflect new state
  - No page reload required
- Clean error handling with `console.error` only

### CSS (`assets/css/tm-mute-menu-notifications.css`)

- Minimal: only styles for the admin bar button icon spacing and any hover/active states
- The actual hiding CSS is injected inline by PHP (server-side), not in this file

### Uninstall (`uninstall.php`)

- Proper WordPress uninstall file (checks `WP_UNINSTALL_PLUGIN`)
- Deletes the `tm_mute_menu_notifications` option
- Replaces the deactivation hook cleanup (deactivation should not delete data; uninstall should)

## Performance Profile

| Metric | Current | After Refactoring |
|--------|---------|-------------------|
| AJAX calls on page load | 1 | 0 |
| JS file cacheability | None (new version every load) | Browser-cacheable (static version) |
| Script position | `<head>` (blocking) | Footer (non-blocking) |
| CSS file HTTP request | 1 (for one rule) | 0 (inline in `admin_head`) |
| Notification hide mechanism | JS DOM manipulation after AJAX | Server-side inline CSS in `<head>` |
| Total HTTP overhead per page | 3 requests (CSS + JS + AJAX) | 1 request (JS only) |

## Security Improvements

- `current_user_can( 'update_plugins' )` on all AJAX handlers
- `check_ajax_referer()` instead of manual `filter_input` + `wp_verify_nonce`
- POST method for state-changing toggle action
- Admin bar button only rendered for capable users
- Assets only enqueued for capable users
- Proper `uninstall.php` instead of deactivation hook for data cleanup

## UI Improvements

- State-aware label: "Mute Notifications" / "Unmute Notifications"
- Dashicon: `dashicons-bell` (unmuted) / `dashicons-hidden` (muted)
- Instant visual toggle on click (no waiting for AJAX response to update UI)
- `display: none` eliminates layout gaps left by `visibility: hidden`
- Button only visible to users who have update capabilities

---

## Implementation Checklist

### Phase 1: Project Setup

- [ ] Set up PSR-4 autoloading in `composer.json` for `TomMcFarlin\MMN\` namespace
- [ ] Add PHPUnit 9.x and Brain Monkey as dev dependencies
- [ ] Add WPCS as dev dependency with custom ruleset
- [ ] Create `phpunit.xml.dist` configuration
- [ ] Create `phpcs.xml.dist` configuration
- [ ] Create `tests/bootstrap.php` with Brain Monkey setup
- [ ] Add Composer scripts: `test`, `lint`, `lint:fix`
- [ ] Create `uninstall.php`
- [ ] Create `readme.txt` (WordPress.org format)
- [ ] Update `CONTRIBUTING.md` with plugin name

### Phase 2: Core Classes

- [ ] Create `src/Plugin.php` with hook registration
- [ ] Create `src/NotificationMuter.php` with option read/toggle and inline CSS injection
- [ ] Create `src/AdminBar.php` with state-aware button, dashicon, capability gate
- [ ] Create `src/Ajax/ToggleHandler.php` with POST, nonce, capability check
- [ ] Refactor `plugin.php` to bootstrap only (constants, autoload, `Plugin::init()`)

### Phase 3: Frontend

- [ ] Rewrite JS: remove AJAX-on-load, POST toggle, `<style>` element swap, label/icon update
- [ ] Update CSS: admin bar icon spacing only (hide rules move to inline PHP)
- [ ] Enqueue JS in footer with static version string
- [ ] Localize script with mute state, nonce, and AJAX URL

### Phase 4: Tests

- [ ] Write `NotificationMuterTest` (option read, toggle, inline CSS output)
- [ ] Write `AdminBarTest` (capability gate, node attributes, state-aware label)
- [ ] Write `ToggleHandlerTest` (nonce failure, capability failure, success toggle)
- [ ] Write `PluginTest` (hook registration verification)
- [ ] Verify all tests pass with `composer test`
- [ ] Verify WPCS compliance with `composer lint`

### Phase 5: Polish

- [ ] Review all selectors against current WordPress 6.9 admin markup
- [ ] Test toggle behavior across page loads (mute, reload, confirm hidden)
- [ ] Test with multiple user roles (admin sees button, editor does not)
- [ ] Update CHANGELOG.md with refactoring changes under `[Unreleased]`
- [ ] Update README.md
- [ ] Final `composer lint` and `composer test` pass
