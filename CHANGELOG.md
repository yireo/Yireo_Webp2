# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.4.1] - July 2019
### Fixed
- Original tag (with custom styling) was not used in `picture` element, but new one was created instead

## [0.4.0] - July 2019
### Added
- Move configuration to separate **Yireo** section
- Add a `config.xml` file
- Changed configuration path from `system/yireo_webp2/*` to `yireo_webp2/settings/*`
- Move `quality_level` to `config.xml`

## [0.3.0] - 2019-05-02
### Fixed
- Fix issue with additional images not being converted if already converted (@jove4015)
- Fix issue with static versioning not being reckognized
- Make sure src, width and height still remain in picture-tag

### Added
- Integration test for multiple instances of same image
- Add fields in backend for PHP version and module version
- Integration Test to test conversion of test-files
- Throw an exception of source file is not found
- Add provider of dummy images
- Add integration test of dummy images page
- Add test page with dummy images
- Only show original image in HTML source when debugging

## [0.2.0] - 2019-04-28
### Fixed
- Fix issue with additional images not being converted if already converted
- Make sure to enable cookie-check whenever FPC is enabled

### Added
- Actual meaningful integration test for browsing product page

## [0.1.1] - 2019-04-12
### Fixed
- Disable gallery fix if FPC is enabled

## [0.1.0] - 2019-04-12
### Added
- Add GD checker in backend settings

## [0.0.3] - 2019-03-21
### Fixed
- Fix for Fotorama gallery
- Check via timestamps for new and modified files

### Added
- Check for browser support via cookie and Chrome-check
- Inspect ACCEPT header for image/webp support
- Implement detect.js script to detect WebP support and set a cookie
- Add configuration values

## [0.0.2] - 2019-03-20
### Added
- Temporary release for CI purpose

## [0.0.1] - 2019-03-20
### Added
- Initial commit
- Support for regular img-tags to picture-tags conversion
