#!/usr/bin/env bash
pushd /tmp/magento2

test -f dev/tests/unit/phpunit.xml || exit 0

#composer require "mustache/mustache":"~2.5"
#composer require "php-coveralls/php-coveralls":"^1.0"

echo "Running unit tests"
cd dev/tests/unit/
../../../vendor/bin/phpunit -c phpunit.xml

popd
    
