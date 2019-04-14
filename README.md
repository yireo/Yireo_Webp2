# Magento 2 module for WebP
This module adds WebP support to Magento 2. Currently, it ships with the following features:

- When `<img>` tags are found on the page, the corresponding JPG or PNG is converted into WebP and a corresponding `<picture` tag is used to replace the original `<img>` tag.
- The Futurama gallery of the Magento core product pages is replaced with WebP images, as long as the Full Page Cache is disabled. Unfortunately, with the FPC, the whole JavaScript needs to be refactored.

### Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present.

Next, install our module using the following command:

    composer require yireo/magento2-webp2

Next, install the new module into Magento itself:

    ./bin/magento module:enable Yireo_WebP2
    ./bin/magento setup:upgrade

Enable the module by toggling the setting in **Stores > Configuration > Advanced > System > Yireo WebP > Enabled**.

Done.
