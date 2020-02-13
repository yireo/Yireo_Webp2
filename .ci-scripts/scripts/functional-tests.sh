#!/usr/bin/env bash
echo 'Creating magento2 database'
mysql -u root --password=root -e 'CREATE DATABASE magento2;'

echo "Installing Magento"
php bin/magento setup:install \
    --language="en_US" \
    --timezone="UTC" \
    --currency="USD" \
    --base-url="http://magento2.test/" \
    --base-url-secure="http://magento2.test/" \
    --use-secure=1 \
    --use-secure-admin=1 \
    --admin-firstname="John" \
    --admin-lastname="Doe" \
    --backend-frontname="admin" \
    --admin-email="admin@example.com" \
    --admin-user="admin" \
    --use-rewrites=1 \
    --db-host=127.0.0.1 \
    --db-name=magento2 \
    --db-user=root \
    --cleanup-database \
    --admin-use-security-key=0 \
    --admin-password="1234admin"

echo "Enabling developer mode"
php bin/magento deploy:mode:set developer
php bin/magento config:set cms/wysiwyg/enabled disabled
php bin/magento config:set admin/security/admin_account_sharing 1
php bin/magento config:set admin/security/use_form_key 0
    
popd
