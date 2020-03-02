#!/usr/bin/env bash
pushd /tmp/magento2

test -f dev/tests/integration/phpunit.xml || exit 0

if [ -z "$MYSQL_HOST" ]; then
    MYSQL_HOST=127.0.0.1
fi

cat dev/tests/integration/etc/install-config-mysql.phps |
    sed -e "s/%MYSQL_HOST%/${MYSQL_HOST}/g" \
    > dev/tests/integration/etc/install-config-mysql.php

echo 'Creating magento-integration-test database'
mysql -u root --password=root -e 'CREATE DATABASE `magento-integration-test`;' || exit 1

echo 'SHOW DATABASES' | mysql -u root --password=root | grep 'magento-integration-test' || exit 1

echo "Running integration tests"
cd dev/tests/integration/
php -d memory_limit=1G ../../../vendor/bin/phpunit -c phpunit.xml
echo "Done running integration tests"
    
popd
