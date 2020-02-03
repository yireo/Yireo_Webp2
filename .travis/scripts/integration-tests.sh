#!/usr/bin/env bash
pushd /tmp/magento2

echo 'Creating magento-integration-test database'
mysql -u root --password=root -e 'CREATE DATABASE `magento-integration-test`;'
echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root

phpenv config-rm xdebug.ini

echo "Running integration tests"
cd dev/tests/integration/
cat phpunit.xml
../../../vendor/bin/phpunit -c phpunit.xml
    
popd
