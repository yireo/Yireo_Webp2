# About this module
- Browser-support for WebP is detected in various ways: A simple check for Chrome, an ACCEPT header sent by the browser and a JavaScript check generating a `webp` cookie for all browsers that remain.
- When `<img>` tags are found on the page, the corresponding JPG or PNG is converted into WebP and a corresponding `<picture` tag is used to replace the original `<img>` tag.
- The Fotorama gallery of the Magento core product pages is replaced with WebP images, as long as the Full Page Cache is disabled. Unfortunately, with the FPC, the whole JavaScript needs to be refactored.

# System requirements
Make sure your PHP environment supports WebP: This means that the function `imagewebp` should exist in PHP. We hope to add more checks for this in the extension itself soon. For now, just open up a PHP `phpinfo()` page and check for WebP support. Please note that installing `libwebp` on your system is not the same as having PHP support WebP. Check the `phpinfo()` file and add new PHP modules to PHP if needed. If in doubt, simple create a PHP script `test.php` and a line `<?php echo (int)function_exists('imagewebp');` to it: A `1` indicates that the function is available, a `0` indicates that it is not.

An alternative is that the `cwebp` binary from the WebP project is uploaded to your server and placed in a generic folder like `/usr/local/bin`. Make sure to grab a copy from this binary from the [rosell-dk/webp-convert](https://github.com/rosell-dk/webp-convert/tree/master/src/Converters/Binaries) project. This method is preferred because it is the fastest. But it assumes also that the binary is placed in a folder by the server administrator.

We recommend you to work on making all options work, not just one.

Please note that both tasks should be simple for developers and system administrator, but might be magical for non-technical people. If this extension is not working out of the box for you, most likely a technical person needs to take a look at your hosting environment.

