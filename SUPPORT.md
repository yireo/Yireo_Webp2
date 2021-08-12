# Requesting support

## First check to see if our extension is doing its job
Before requesting support, please make sure that you properly check whether this extension is working or not. Do not look at your browser and definitely do not use third party SEO tools to make any conclusions. Instead, inspect the HTML source in your browser. Our extension modifies the HTML so that regular `<img>` tags are replaced with something similar to the following:
```html
<picture>
  <source type="image/webp" srcset="example.webp">
  <source type="image/png" srcset="example.png">
  <img src="example.png" />
</picture>
```

If similar HTML is there, but your browser is not showing the WebP image, then realize that this is not due to our extension, but due to your browser. Unfortunately, we are not browser manufacturers and we can't change this. Refer to https://caniuse.com/#search=webp instead.

## Opening an issue for this extension
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

## Issues with specific images
If some images are converted but others are not, please supply the following details:

- The output of the command `bin/magento next-gen-images:test-uri $URL` where `$URL` is the URL to the original image (JPG or PNG)
- The output of the command `bin/magento next-gen-images:convert $PATH` where `$PATH` is the absolute path to the original image (JPG or PNG)
- Whether or not you are using a CDN for serving images.