#!/usr/bin/env bash
#
# Travis installation script run before running main script
#

if [ -n "$MAGENTO_USERNAME" ]; then
    composer config --global http-basic.repo.magento.com "$MAGENTO_USERNAME" "$MAGENTO_PASSWORD"
fi

git clone --depth 1 https://github.com/magento/magento2:${MAGENTO_VERSION} /tmp/magento2

source .module.ini
mkdir -p /tmp/magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}
cp -R * /tmp/magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}/

pushd /tmp/magento2
composer install --prefer-dist
popd

