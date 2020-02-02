#!/usr/bin/env bash
source ../.module.ini
mkdir -p magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}
mv * magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}/

pushd magento2
composer install
popd
