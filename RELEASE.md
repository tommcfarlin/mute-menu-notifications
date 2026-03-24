# Release Checklist

## Pre-Release

On the `release-X.Y.Z` branch:

- [ ] Update `[Unreleased]` heading in CHANGELOG.md to `[X.Y.Z] - YYYY-MM-DD`
- [ ] Update version number in the main plugin file header
- [ ] Ensure `Tested up to` is current in the plugin file header
- [ ] Run `composer test` and confirm all tests pass
- [ ] Run `composer lint` and confirm WPCS compliance
- [ ] Open a PR from `release-X.Y.Z` into `main`

## Merge and Tag

- [ ] Merge the release PR into `main`
- [ ] Tag the release on GitHub (`vX.Y.Z`)
- [ ] Upload the plugin ZIP as a release asset

## Post-Release

- [ ] Merge `main` back into `develop`
- [ ] Delete the `release-X.Y.Z` branch
