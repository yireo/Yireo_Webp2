# About this module
- When `<img>` tags are found on the page, the corresponding JPG or PNG is converted into WebP and a corresponding `<picture` tag is used to replace the original `<img>` tag.
- The Fotorama gallery of the Magento core product pages is replaced with WebP images without issues as well. However, the Fotorama effect loads new JPG images again, replacing the original `<picture>` tag. This shows that the Fotorama library is not scalable and may be a bad decision to use. We recommend you replace it with the [Notorama](https://github.com/robaimes/module-notorama) module instead.

# System requirements
Make sure your PHP environment supports WebP: This means that the function `imagewebp` should exist in PHP. We hope to add
more checks for this in the extension itself soon. For now, just open up a PHP `phpinfo()` page and check for WebP
support. Please note that installing `libwebp` on your system is not the same as having PHP support WebP. Check the
`phpinfo()` file and add new PHP modules to PHP if needed. If in doubt, simple create a PHP script `test.php` and a line
`<?php echo (int)function_exists('imagewebp');` to it: A `1` indicates that the function is available, a `0` indicates
that it is not. Alternatively you can check for WebP using the command `php -r 'var_dump(gd_info());'`. Make sure your CLI
binary is the same as the one being called by the webserver though.

An alternative is that the `cwebp` binary from the WebP project is uploaded to your server and placed in a generic folder like `/usr/local/bin`. Make sure to grab a copy from this binary from the [rosell-dk/webp-convert](https://github.com/rosell-dk/webp-convert/tree/master/src/Convert/Converters/Binaries) project. This method is preferred because it is the fastest. But it assumes also that the binary is placed in a folder by the server administrator.

We recommend you to work on making all options work, not just one.

Please note that both tasks should be simple for developers and system administrator, but might be magical for non-technical people. If this extension is not working out of the box for you, most likely a technical person needs to take a look at your hosting environment.

### GraphQL support
This module enhances GraphQL as well:
```graphql
query {
  products(search:"a", pageSize: 1){
    items {
      sku
      name
      media_gallery {
        url
        url_webp
      }
    }
  }
}
```
