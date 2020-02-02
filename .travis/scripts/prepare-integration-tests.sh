#!/usr/bin/env bash
echo 'Creating magento2 database'
mysql -uroot -e 'CREATE DATABASE magento2;'

phpenv config-rm xdebug.ini

echo "Enabling developer mode"
php bin/magento deploy:mode:set developer
 
popd
