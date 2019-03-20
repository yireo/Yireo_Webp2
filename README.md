# Magento 2 module for WebP2
(in development)

### Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present.

Next, install our module using the following command:

    composer require --dev yireo/magento2-webp2

Next, install the new module into Magento itself:

    ./bin/magento module:enable Yireo_WebP2
    ./bin/magento setup:upgrade

Check whether the module is succesfully installed in **Admin > Stores > Configuration > Advanced > Advanced**.

Done.

