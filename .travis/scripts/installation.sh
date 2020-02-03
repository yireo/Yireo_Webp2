#!/usr/bin/env bash
pushd /tmp/magento2

echo 'Creating magento2 database'
mysql -u root --password=root -e 'CREATE DATABASE `magento2`;'

phpenv config-rm xdebug.ini

echo "Installing Magento"
php bin/magento setup:install \
    --language="en_US" \
    --timezone="UTC" \
    --currency="USD" \
    --base-url="${MAGENTO_PROTOCOL}://${MAGENTO_HOST_NAME}/" \
    --base-url-secure="${MAGENTO_PROTOCOL}://${MAGENTO_HOST_NAME}/" \
    --use-secure=1 \
    --use-secure-admin=1 \
    --admin-firstname="John" \
    --admin-lastname="Doe" \
    --backend-frontname="${MAGENTO_BACKEND}" \
    --admin-email="admin@example.com" \
    --admin-user="${MAGENTO_ADMIN_USERNAME}" \
    --use-rewrites=1 \
    --db-host=127.0.0.1 \
    --db-name=magento2 \
    --db-user=root \
    --db-password=root \
    --cleanup-database \
    --admin-use-security-key=0 \
    --admin-password="${MAGENTO_ADMIN_PASSWORD}"

echo "Enabling developer mode"
php bin/magento deploy:mode:set developer
php bin/magento setup:di:compile
php bin/magento deploy:mode:set developer

echo "Enabling production mode"
php bin/magento deploy:mode:set production
    
popd
