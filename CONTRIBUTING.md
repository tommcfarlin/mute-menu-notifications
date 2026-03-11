# Contributing to [Plugin Name]

Thank you for your interest in contributing. This document outlines the workflow and conventions used for development.

## Branching Strategy

This project uses the following branch structure:

- **master** -- production-ready code; tagged releases are cut from here
- **develop** -- integration branch for the next release
- **release-X.Y.Z** -- release preparation branches, created from `develop`
- **Issue branches** -- feature/fix/enhancement work, branched from the current `release-X.Y.Z`

## Branch Naming

Branch names follow this pattern:

```
<type>/issue-<number>-<short-description>
```

Where `<type>` matches the issue label:

| Type          | Prefix          |
|---------------|-----------------|
| Feature       | `feature/`      |
| Enhancement   | `enhancement/`  |
| Fix           | `fix/`          |
| Research      | `research/`     |
| Chore         | `chore/`        |

Example: `fix/issue-42-modal-not-closing`

## Issue Workflow

1. Create an issue using one of the provided templates.
2. Branch from the current `release-X.Y.Z` branch using the naming convention above.
3. Do your work, committing with [Conventional Commits](https://www.conventionalcommits.org/) style messages.
4. Open a pull request into `develop`.
5. Fill out the PR template, request review, and address feedback.
6. Once approved, the PR is merged into `develop`.

## Pull Request Workflow

When opening a PR:

1. Fill out the PR template completely.
2. Link the related issue (`Closes #<number>`).
3. Update `CHANGELOG.md` with your changes under `[Unreleased]`.
4. Update `readme.txt` changelog section if the change is user-facing.
5. Ensure all checks pass:
   - `composer test` -- run the test suite
   - `composer lint` -- check WordPress Coding Standards compliance
6. Request a review.

## Changelog Maintenance

This project uses [Keep a Changelog](https://keepachangelog.com/) format with [Semantic Versioning](https://semver.org/).

- Add entries under the `[Unreleased]` heading in `CHANGELOG.md`.
- Group changes under: `Added`, `Changed`, `Deprecated`, `Removed`, `Fixed`, `Security`.
- Entries move from `[Unreleased]` to a versioned heading at release time.

## Code Standards

- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/) for PHP, JS, CSS, and HTML.
- Run `composer lint` to check compliance before opening a PR.
- Run `composer test` to ensure all tests pass.
