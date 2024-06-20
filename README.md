# Magento 2 module for WebP
<img src="https://img.shields.io/packagist/dt/yireo/magento2-webp2"/> <img src="https://img.shields.io/packagist/v/yireo/magento2-webp2"/> <img src="https://img.shields.io/github/languages/top/yireo/Yireo_Webp2"/> <img src="https://img.shields.io/github/last-commit/yireo/Yireo_Webp2" /> <img src="https://img.shields.io/github/sponsors/yireo"/> <img src="https://img.shields.io/twitter/follow/yireo?style=social" />

**This module adds WebP support to Magento 2.**

## About this module
- When `<img>` tags are found on the page, the corresponding JPG or PNG is converted into WebP and a corresponding `<picture` tag is used to replace the original `<img>` tag.
- The Fotorama gallery of the Magento core product pages is replaced with WebP images without issues as well. However, the Fotorama effect loads new JPG images again, replacing the original `<picture>` tag. This shows that the Fotorama library is not scalable and may be a bad decision to use. We recommend you replace it with the [Notorama](https://github.com/robaimes/module-notorama) module instead.

Hyvä support is built into the latest versions of this module. Please do not use the deprecated compatibility module anymore.

## Instructions for using composer
Use composer to install this extension. Not using composer is **not** supported. Next, install the new module plus its dependency `Yireo_NextGenImages` into Magento itself: 

```bash
composer require yireo/magento2-webp2
bin/magento module:enable Yireo_Webp2 Yireo_NextGenImages
bin/magento setup:upgrade
```

Enable the module by toggling the setting in **Stores > Configuration > Yireo > Yireo WebP > Enabled**.

## System requirements
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

## FAQ

#### Does this module support GraphQL?
Yes, but only via the additional [Yireo Webp2GraphQl](https://github.com/yireo/Yireo_Webp2GraphQl) module

#### How do I know WebP is used?
Make sure to test things with the obvious caches disabled (Full Page Cache, Block HTML Cache). Once this extension is working, catalog images (like on a category page) should be replaced with: Their `<img>` tag should be replaced with a `<picture>` tag that lists both the old image and the new WebP image. If the conversion from the old image to WebP goes well.

You can expect the HTML to be changed, so inspecting the HTML source gives a good impression. You can also use the Error Console to inspect network traffic: If some `webp` images are flying be in a default Magento environment, this usually proofs that the extension is working to some extent.

#### My CPU usage goes up. Is that normal?
Yes, it is normal. This extension does two things: It shows a WebP on the frontend of your shop. And it
generates that WebP when it is missing. Obviously, generating an image takes up system resources. And if
you have a large catalog, it is going to do more time. How much time? Do make sure to calculate this
yourself: Take an image, resize it using the `cwebp` binary and measure the time - multiply it by how many
images there are. This should give a fair estimation on how much time is needed.

Note that this extension allows for using various mechanisms (aka *convertors*). Tune the **Convertors**
settings if you want to try to optimize things. Sometimes, GD is faster than `cwebp`. Sometimes, GD just
breaks things. It depends, so you need to pick upon the responsibility to try this in your specific
environment.

Also note that this extension allows for setting an *encoding*. The default is `auto` which creates both a lossy and a lossless WebP and then picks the smallest one. Things could be twice as fast by setting this to `lossy`.

If you don't like the generation of images at all, you could also use other CLI tools instead.

#### Class 'WebPConvert\WebPConvert' not found
We only support the installation of our Magento 2 extensions, if they are installed via `composer`. Please note that - as we see it - `composer` is the only way for managing Magento depedencies. If you want to install the extension manually in `app/code`, please study the contents of `cmoposer.json` to install all dependencies of this module manually as well.

#### After installation, I'm still seeing only PNG and JPEG images
This could mean that the conversion failed. New WebP images are stored in the same path as the original path (somewhere in `pub/`) which means that all folders need to be writable by the webserver. Specifically, if your deployment is based on artifacts, this could be an issue.

Also make sure that your PHP environment is capable of WebP: The function `imagewebp` should exist in PHP and we recommend a `cwebp` binary to be placed in `/usr/local/bin/`.

Last but not least, WebP images only work in WebP-capable browsers. The extension detects the browser support. Make sure to test this in Chrome first, which natively supports WebP.

#### Warning: filesize(): stat failed for xxx.webp
If you are seeing this issue in the `exception.log` and/or `system.log`,
do make sure to wipe out Magento caching and do make sure that the WebP
file in question is accessible: The webserver running Magento should have
read access to the file. Likewise, if you want this extension to
automatically convert a JPEG into WebP, do make sure that the folder
containing the JPEG is writable.

#### Some of the images are converted, but others are not.
Not all JPEG and PNG images are fit for conversion to WebP. In the past, WebP has had issues with alpha-transparency and partial transparency. If the WebP image can't be generated by our extension, it is not generated. Simple as that. If some images are converted but some are not, try to upload those to online conversion tools to see if they work.

Make sure your `cwebp` binary and PHP environment are up-to-date.

#### This sucks. It only works in some browsers.
Don't complain to us. Instead, ask the other browser vendors to support this as well. And don't say this
is not worth implementing, because I bet more than 25% of your sites visitors will benefit from WebP. Not
offering this to them, is wasting additional bandwidth.

#### Some emails are also displaying WebP
It could be that your transactional email templates are including XML layout handles that suddenly introduce this extensions functionality, while actually adding WebP to emails is a bad idea (because most email clients will not support anything of the like).

If you encounter such an email, find out which XML layout handle is the cause of this (`{handle layout="foobar"}`). Next, create a new XML layout file with that name (`foobar.xml`) and call the `webp_skip` handle from it (`<update handle="webp_skip" />`). So this instructs the WebP extension to skip loading itself.

#### error while loading shared libraries: libjpeg.so.8: cannot open shared object file: No such file or directory
Ask your system administrator to install this library. Or ask the system administrator to install WebP support in PHP by upgrading PHP itself to the right version and to include the right PHP modules (like imagemagick). Or skip converting WebP images by disabling the setting in our extension and then convert all WebP images by hand.

#### Can I use this with Amasty Shopby?
Yes, you can. Make sure to install the addition https://github.com/yireo/Yireo_Webp2ForAmastyShopby as well.

#### How can I convert WebP images manually from the CLI?
Even though this extension (or actually its parent extension `Yireo_NextGenImages`) support a CLI (`bin/magento next-gen-images:convert`), using this extension for 1000s of images will definitely not be performant. If you want to convert all images at once from the CLI, it is better to look at other tools. For instance, the `cwebp` binary (part of the Google WebP project itself) could be installed. Or perhaps you could use `convert` of the Imagick project. 

Next, a simple shell script could be used to build all WebP files:
```bash
find . -type f -name \*.jpg | while read IMAGE do; convert $IMAGE ${IMAGE/.jpg/.webp}; done
```

Or another example (of @rostilos):
```bash
#!/bin/bash
start=`date +%s`
directory="../pub/media"

cd "$directory" || exit
find . -type f \( -iname \*.jpg -o -iname \*.jpeg -o -iname \*.png \) -print0 |

while IFS= read -r -d $'\0' file;
  do
    filename=$(basename -- "$file")
    new_filename="${filename%.*}.webp"
    new_filepath="$(dirname "$file")/$new_filename"
    echo "Converting: $file -> $new_filepath"
    cwebp -q 80 -quiet "$file" -o "$new_filepath"
  done
end=`date +%s`
runtime=$((end-start))

echo "Execution completed in $runtime seconds."
```

## Requesting support

### First check to see if our extension is doing its job
Before requesting support, please make sure that you properly check whether this extension is working or not. Do not look at your browser and definitely do not use third party SEO tools to make any conclusions. Instead, inspect the HTML source in your browser. Our extension modifies the HTML so that regular `<img>` tags are replaced with something similar to the following:
```html
<picture>
  <source type="image/webp" srcset="example.webp">
  <source type="image/png" srcset="example.png">
  <img src="example.png" />
</picture>
```

If similar HTML is there, but your browser is not showing the WebP image, then realize that this is not due to our extension, but due to your browser. Unfortunately, we are not browser manufacturers and we can't change this. Refer to https://caniuse.com/#search=webp instead.

### Opening an issue for this extension
Feel free to open an **Issue** in the GitHub project of this extension. However, do make sure to be thorough and provide as many details as you can:

- What browser did you test this with?
- What is your Magento version?
- What is your PHP version?
- Did you make sure to use `composer` to install all dependencies?
    - Not using `composer` is **not** supported.
- Which specific composer version of the Yireo WebP2 extension did you install?
- Which specific composer version of the Yireo NextGenImages extension did you install?
- Have you tested this after flushing all caches, especially the Full Page Cache?
- Have you tested this after disabling all caches, especially the Full Page Cache?
    - The Full Page Cache will prevent you from seeing dynamic results. The Full Page Cache works with our extension, it just pointless to troubleshoot things with FPC enabled, unless you are troubleshooting FPC itself.
- Are you testing this in the Developer Mode or in the Production Mode?
    - We recommend you to use the Developer Mode. Do not use the Default Mode anywhere. Do not use the Production Mode to make modifications to Magento.
- Under **Stores > Configuration > Yireo > Yireo WebP2** and **Stores > Configuration > Yireo > Yireo NextGenImages**, what settings do you see with which values?
    - Preferably post a screenshot with the settings.
- Could you supply a URL to a live demo?
- Could you please supply a snapshot of the HTML source of a product page?

Please note that we do not support Windows to be used to run Magento. Magento itself is not supporting Windows either. If you are stuck with Windows, make sure to use the WSL (Windows Subsystem for Linux) and use the Linux layer instead.

If the WebP configuration section is showing in the backend, but the HTML source in the frontend is not modified, please send the output of the command `bin/magento dev:di:info "\Magento\Framework\View\LayoutInterface"`.

### Issues with specific images
If some images are converted but others are not, please supply the following details:

- The output of the command `bin/magento next-gen-images:test-uri $URL` where `$URL` is the URL to the original image (JPG or PNG)
- The output of the command `bin/magento next-gen-images:convert $PATH` where `$PATH` is the absolute path to the original image (JPG or PNG)
- Whether or not you are using a CDN for serving images.
