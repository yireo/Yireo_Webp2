# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

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
