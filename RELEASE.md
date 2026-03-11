# Release Checklist

## Pre-Release

On the `release-X.Y.Z` branch:

- [ ] Update `[Unreleased]` heading in CHANGELOG.md to `[X.Y.Z] - YYYY-MM-DD`
- [ ] Update version number in the main plugin file header
- [ ] Update `Stable tag` in readme.txt
- [ ] Ensure `Tested up to` is current in both the plugin file and readme.txt
- [ ] Update the `Changelog` section in readme.txt
- [ ] Run `composer test` and confirm all tests pass
- [ ] Run `composer lint` and confirm WPCS compliance
- [ ] Open a PR from `release-X.Y.Z` into `master`

## Merge and Tag

- [ ] Merge the release PR into `master`
- [ ] Tag the release on GitHub (`vX.Y.Z`)
- [ ] Verify the deploy action pushes to WordPress.org SVN successfully
- [ ] Confirm the updated plugin appears on WordPress.org

## Post-Release

- [ ] Merge `master` back into `develop`
- [ ] Delete the `release-X.Y.Z` branch

## First-Time Setup

These steps are only needed once, after WordPress.org approval:

- [ ] Add `SVN_USERNAME` secret to the GitHub repository
- [ ] Add `SVN_PASSWORD` secret to the GitHub repository
