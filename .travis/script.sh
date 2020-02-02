#!/usr/bin/env bash

set -e
pushd magento2

if [ $TEST_SUITE == 'unit' ]; then
    vendor/bin/phpunit -c .magento/dev/tests/unit/phpunit.xml;
fi

if [ $TEST_SUITE == 'phpstan' ]; then
    composer require --dev phpstan/phpstan fooman/phpstan-magento2-magic-methods;
    vendor/bin/phpstan analyse -l 2 app/code/Yireo/* -a dev/tests/api-functional/framework/autoload.php;
fi

if [ $TEST_SUITE == 'static' ]; then
    vendor/bin/phpcs --standard=dev/tests/static/framework/Magento/ app/code/Yireo*;
fi

if [ $TEST_SUITE == 'integration' ]; then
    vendor/bin/phpunit -c .magento/dev/tests/integration/phpunit.xml;
fi

popd
