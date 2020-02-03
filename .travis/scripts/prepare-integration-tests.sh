#!/usr/bin/env bash
pushd /tmp/magento2

echo 'Creating magento-integration-test database'
mysql -uroot -e 'CREATE DATABASE `magento2`;'
mysql -uroot -e 'CREATE DATABASE `magento-integration-test`;'
echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root

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
    
echo "Running integration tests"
cd dev/tests/integration/
cat phpunit.xml
../../../vendor/bin/phpunit -c phpunit.xml
    
popd
