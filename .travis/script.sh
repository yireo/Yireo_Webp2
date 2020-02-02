#!/usr/bin/env bash
#
# Travis main script
#

set -e
cp -R .magento/* /tmp/magento2/

pushd /tmp/magento2

if [ $TEST_SUITE == 'unit' ]; then
    echo "Prepare for running unit tests"
    source ${TRAVIS_BUILD_DIR}/.travis/scripts/prepare-unit-tests.sh
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
    source ${TRAVIS_BUILD_DIR}/.travis/scripts/prepare-integration-tests.sh
fi

popd
