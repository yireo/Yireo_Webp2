# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.10.0] - 28 January 2021
### Added
- Improved support on product detail page (configurable swatches + fotorama)

## [0.9.6] - 22 January 2021
### Fixed
- When no .webp file is available, the source tag will be removed to prevent old image being shown on the frontend (@ar-vie)


## [0.9.5] - 3 December 2020
### Fixed
- Remove class UrlReplacer again after refactoring NextGenImages

## [0.9.4] - 3 December 2020
### Fixed
- Fix missing class UrlReplacer

## [0.9.3] - 2 December 2020
### Fixed
- Fix composer deps with magento2-next-gen-images

## [0.9.2] - 30 November 2020
### Fixed
- Stick to new ConvertorInterface interface of NextGenImages

## [0.9.1] - 30 November 2020
### Fixed
- Wrong composer dependency with NextGenImages

## [0.9.0] - 30 November 2020
### Removed
- Remove JavaScript check and Browser class
- Moved code from this module to generic NextGenImages module
- Remove Travis CI script

## [0.8.0] - 30 November 2020
### Fixed
- Lazy loading configuration option was not working

### Added
- Add new option for quality level

## [0.7.6] - 19 August 2020
### Fixed
- Fixes wrong name in require-js config (@johannessmit)

## [0.7.5] - 13 August 2020
### Fixed
- Add jQuery Cookie to prevent load order issues (@basvanpoppel)

## [0.7.4] - 3 August 2020
### Fixed
- Image is correctly updated including the source (@johannessmit)

### Removed
- Undo of `parse_url` Laminas replacement because this breaks pre-Laminas Magento releases

## [0.7.3] - 29 July 2020
### Added
- Magento 2.4 support 

### Fixed
- Numerous PHPCS issues

### Added
- URL parsing via laminas/laminas-uri package

## [0.7.2] - June 13th, 2020
### Fixed
- Remove alt attribute from picture element (@gjportegies)

## [0.7.1] - April 6th, 2020
### Added
- Quick fix to allow for WebP images in catalog swatches

## [0.7.0] - March 31st, 2020
### Added
- Code refactoring to allow for Amasty Shopby compatibility

## [0.6.2] - March 18th, 2020
### Fixed
- Skip captcha images (PR from @jasperzeinstra)

## [0.6.1] - March 18th, 2020
### Fixed
- Do not throw error in debugging with non-existing images
- Rename "Debug Log" to "Debugging" because that's what's happening

## [0.6.0] - March 7th, 2020
### Added
- Skipping WebP image creation by configuration option

## [0.5.2] - March 5th, 2020
### Fixed
- Fix next tag not being closed properly

## [0.5.1] - February 19th, 2020
### Fixed
- Prevent overwriting existing `picture` tags

## [0.5.0] - February 11th, 2020
### Added
- Upgraded rosell-dk/webp-convert library

## [0.4.7] - January 31st, 2020
### Added
- Add class attribute in picture (PR from itsazzad)
- Better support info
- Raise framework requirements for proper support of ViewModels
- Added logic for fetching the css class from the original image (PR from duckchip)
- Added missing  variable in the ReplaceTags plugin (PR from duckchip)

## [0.4.6] - November 23rd, 2019
### Fixed
- Raise requirements for proper support of ViewModels

## [0.4.5] - October 16th, 2019
### Added
- Add check for additional layout handle `webp_skip` 

## [0.4.4] - October 15th, 2019
### Fixed
- Dirty workaround for email layout 
 
## [0.4.3] - October 4th, 2019
### Added
- Add controller for email testing

### Fixed
- Do not apply WebP if no handles

## [0.4.2] - July 14th, 2019
### Fixed
- Make sure modified gallery images are returned as Collection, not an array
- Test with Aimes_Notorama

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
