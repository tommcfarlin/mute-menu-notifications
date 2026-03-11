# Mute Menu Notifications - Project Instructions

## Plugin Overview

WordPress admin plugin that lets administrators hide update notification badges from the admin menu. Site-wide toggle stored as a WordPress option.

## Conventions

- **Namespace**: `TomMcFarlin\MMN`
- **PHP**: 7.4 minimum, strict OOP, PSR-4 autoloading under `src/`
- **Coding Standards**: WordPress Coding Standards (WPCS) enforced via `composer lint`
- **Testing**: PHPUnit 9.x with Brain Monkey, run via `composer test`
- **License**: GPL-3.0
- **Version Control**: Git with Conventional Commits (commitlint format)
- **Branching**: `main` (production), `develop` (integration), `release-X.Y.Z` (release prep), issue branches from release

## Key Files

- `plugin.php` -- Bootstrap (constants, autoloader, `Plugin::init()`)
- `src/Plugin.php` -- Hook coordinator
- `src/NotificationMuter.php` -- Core mute logic, inline CSS injection
- `src/AdminBar.php` -- Admin bar toggle button with state-aware UI
- `src/Ajax/ToggleHandler.php` -- AJAX POST handler with capability + nonce checks
- `uninstall.php` -- Option cleanup on uninstall

## Refactoring Plan

See `docs/REFACTORING-PLAN.md` for the full architecture plan and implementation checklist.

## Personas

Use these personas when reviewing code from different perspectives:

- `personas/plugin-developer.md` -- Senior WordPress plugin developer (architecture, hooks, WPCS, testability)
- `personas/performance-engineer.md` -- Performance engineer (queries, caching, asset loading, profiling)
- `personas/security-reviewer.md` -- Security reviewer (input handling, XSS, CSRF, authorization)
- `personas/ui-ux-designer.md` -- UI/UX designer (native feel, accessibility, progressive disclosure, feedback)

## Workflow

- `CONTRIBUTING.md` -- Branching strategy, issue workflow, PR process, code standards
- `RELEASE.md` -- Release checklist (pre-release, merge/tag, post-release)
- `.github/PULL_REQUEST_TEMPLATE.md` -- PR template
- `.github/ISSUE_TEMPLATE/` -- Issue templates (bug, enhancement, feature, chore, research)
- `scripts/setup-labels.sh` -- GitHub label setup script

## Rules

- Do not add "Co-authored-by" lines to commit messages
- Do not use emoji in PR descriptions or commit messages
- Always use commitlint formatted commits
- This plugin is NOT yet in the WordPress.org plugin repository
- Site-wide option (not per-user) for the mute preference
- Only users with `update_plugins` capability should see the toggle or access AJAX endpoints
