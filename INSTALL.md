# Instructions for using composer
Use composer to install this extension. First make sure that Magento is installed via composer, and that there is a valid `composer.json` file present.

Next, install our module using the following command:

    composer require yireo/magento2-webp2

Next, install the new module plus its dependency `Yireo_NextGenImages` into Magento itself:

    ./bin/magento module:enable Yireo_Webp2 Yireo_NextGenImages
    ./bin/magento setup:upgrade

Enable the module by toggling the setting in **Stores > Configuration > Yireo > Yireo WebP > Enabled**.

# Instructions for using ExtDN installer
First install the ExtDN installer:

	wget https://github.com/extdn/installer-m2/raw/master/build/extdn_installer.phar
	chmod 755 extdn_installer.phar 

We recommend moving the PHAR file to a global location like `/usr/local/bin/extdn_installer`:

    sudo mv extdn_installer.phar /usr/local/bin/extdn_installer

Next, install this extension:

	extdn_installer install yireo/magento2-webp2

Done.

