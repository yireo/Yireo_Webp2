#!/usr/bin/env bash
pushd /tmp/magento2

test -f dev/tests/integration/phpunit.xml || exit 0

echo 'Creating magento-integration-test database'
mysql -u root --password=root -e 'CREATE DATABASE `magento-integration-test`;'

echo "Running integration tests"
cd dev/tests/integration/
php ../../../vendor/bin/phpunit -c phpunit.xml
    
popd
