# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.13.5] - 4 April 2024
### Fixed
- Compatibility with Magento >=2.4.7-beta3

## [0.13.4] - 26 September 2023
### Fixed
- Catch the correct exception when using properties of a specific exception.

## [0.13.3] - 22 September 2023
### Fixed
- Properly check for MIME-typed (fix of #149)
- Add "webp" / "nowebp" to body class for Hyva

## [0.13.2] - 6 September 2023
### Fixed
- Make sure convertor is called for unsupported types #149 (@kernstmediarox)

## [0.13.1] - 6 September 2023
### Fixed
- Remove non-existing `description` in exception handling
- Move Hyva dependency to separate package `yireo/magento2-webp2-for-hyva`

## [0.13.0] - 30 August 2023
### Added
- Copied GraphQL `url_webp` from Hyva compatibility module to here

### Fixed
- Catch exceptions in converters by changing array to string with a foreach (@ghezelbash PR #140)

## [0.12.5] - 19 March 2023
### Fixed
- Version bump because of new NextGenImages minor

## [0.12.4] - 13 September 2022
### Fixed
- Implement the NextGenImages config setting `convert_images`

## [0.12.3] - 20 August 2022
### Fixed
- Add missing semicolon #130

## [0.12.2] - 21 July 2022
### Fixed
- Using JS native `_determineProductData` function #120 (@artbambou)
- Fix uncaught exception #127 (@jakegore)

## [0.12.1] - 2 June 2022
### Fixed
- Skip image replacement if target URL is still empty

## [0.12.0] - 4 May 2022
### Added
- Added ACL for user permissions (@jeroenalewijns)
- Always add `webp` CSS class to body via JS detection
- Add various integration tests
- Refactoring because of changed NextGenImages API

### Removed
- Moved AJAX plugin for swatches to NextGenImages

## [0.11.4] - 23 August 2021
### Fixed
- Check for double InvalidInputImageTypeException

## [0.11.3] - 23 August 2021
### Fixed
- Fix wrong exception being caught

## [0.11.2] - 11 August 2021
### Fixed
- Remove async from function (IE11 Support) (@barryvdh)

## [0.11.1] - 10 August 2021
### Fixed
- Fix error to config.xml introduced in last version (@chrisastley)

## [0.11.0] - 9 August 2021
### Added
- New option `encoding` for better performance with only `lossy` (@basvanpoppel)

## [0.10.11] - 15 July 2021
### Fixed
- Prevent exception when GIF is renamed to JPG

## [0.10.10] - 7 July 2021
### Fixed
- Hide all other fields if setting "Enabled" is set to 0
- Add current store to all methods

## [0.10.9] - 24 June 2021
### Fixed
- Fix PHP Notice when gallery is missing

## [0.10.8] - 24 June 2021
### Fixed
- Allow configuring convertors (#77)

## [0.10.7] - 6 May 2021
### Fixed
- Prevents error if variable $imageData[$imageType] is empty (@maksymcherevko)

## [0.10.6] - 2 April 2021
### Fixed
- Webp images not being generated if cached jpg does not exist yet #70 (@gtlt)

## [0.10.5] - 9 March 2021
### Fixed
- Refactor conversion errors
- Move helper function to NextGenImages
- Throw exception when no conversion is needed
- Update convertor class to comply to updated interface

## [0.10.4] - 15 February 2021
### Fixed
- Log all exceptions to NextGenImages logger

## [0.10.3] - 12 February 2021
### Fixed
- Make sure configurable images don't throw exception

## [0.10.2] - 12 February 2021
### Fixed
- Make sure wrong gallery images don't throw exception

## [0.10.1] - 11 February 2021
### Fixed
- Fix JS error
- Rewrite into synchronous way of detecting WebP


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
