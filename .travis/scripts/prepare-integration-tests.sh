#!/usr/bin/env bash
pushd /tmp/magento2

echo 'Creating magento-integration-test database'
mysql -uroot -e 'CREATE DATABASE magento-integration-test;'
echo "USE mysql;\nUPDATE user SET password=PASSWORD('password') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root

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
    --cleanup-database \
    --admin-use-security-key=0 \
    --admin-password="${MAGENTO_ADMIN_PASSWORD}"

echo "Enabling developer mode"
php bin/magento deploy:mode:set developer
php bin/magento config:set cms/wysiwyg/enabled disabled
php bin/magento config:set admin/security/admin_account_sharing 1
php bin/magento config:set admin/security/use_form_key 0
    
ls vendor/
ls vendor/bin
vendor/bin/phpunit -c dev/tests/integration/phpunit.xml;
    
popd
