#!/usr/bin/env bash
#
# Travis main script
#

set -e
pushd /tmp/magento2

if [ $TEST_SUITE == 'unit' ]; then
    echo "Prepare for running unit tests"
    source ${TRAVIS_BUILD_DIR}/.travis/scripts/unit-tests.sh
    vendor/bin/phpunit -c .magento/dev/tests/unit/phpunit.xml;
fi

if [ $TEST_SUITE == 'static' ]; then
    composer require --dev phpstan/phpstan fooman/phpstan-magento2-magic-methods;
    vendor/bin/phpstan analyse -l 2 app/code/* -a dev/tests/api-functional/framework/autoload.php;
    vendor/bin/phpcs --standard=dev/tests/static/framework/Magento/ app/code/*;
fi

if [ $TEST_SUITE == 'integration' ]; then
    source ${TRAVIS_BUILD_DIR}/.travis/scripts/integration-tests.sh
fi

if [ $TEST_SUITE == 'functional' ]; then
    source ${TRAVIS_BUILD_DIR}/.travis/scripts/functional-tests.sh
fi

if [ $TEST_SUITE == 'installation' ]; then
    source ${TRAVIS_BUILD_DIR}/.travis/scripts/installation.sh
fi

popd
