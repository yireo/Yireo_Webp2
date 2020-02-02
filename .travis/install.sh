#!/usr/bin/env bash
#
# Travis installation script run before running main script
#

composer config --global http-basic.repo.magento.com "$MAGENTO_USERNAME" "$MAGENTO_PASSWORD"
git clone --depth 1 https://github.com/magento/magento2

source .module.ini
mkdir -p magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}
cp -R * magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}/

pushd magento2
composer install
popd

