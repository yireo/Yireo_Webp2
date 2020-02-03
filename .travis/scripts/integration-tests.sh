#!/usr/bin/env bash
pushd /tmp/magento2

test -f dev/tests/integration/phpunit.xml || exit 0

echo 'Creating magento-integration-test database'
mysql -u root --password=root -e 'CREATE DATABASE `magento-integration-test`;'

phpenv config-rm xdebug.ini

echo "Running integration tests"
cd dev/tests/integration/
cat phpunit.xml
../../../vendor/bin/phpunit -c phpunit.xml
    
popd
