#!/bin/bash
#
# Travis installation script run before running main script
#

if [ -z "$MAGENTO_VERSION" ]; then
    echo "No MAGENTO_VERSION variable defined. Exiting"
    exit 1
fi

if [ -z "$TEST_SUITE" ]; then
    echo "No TEST_SUITE variable defined. Exiting"
    exit 1
fi

if [ -z "$TRAVIS_BUILD_DIR" ]; then
    TRAVIS_BUILD_DIR=/source
fi

if [[ ${TEST_SUITE} == "functional" ]]; then
    source .ci-scripts/scripts/mock-mail.sh
fi

if [ -n "$MAGENTO_USERNAME" ]; then
    composer config --global http-basic.repo.magento.com "$MAGENTO_USERNAME" "$MAGENTO_PASSWORD"
fi

git clone --single-branch --branch ${MAGENTO_VERSION} https://github.com/magento/magento2 /tmp/magento2

test -f /tmp/magento2/composer.json || exit 1

echo "Reset root password to root"
echo "USE mysql;\nUPDATE user SET authentication_string=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root

source .module.ini
test -z "${COMPOSER_NAME}" && exit 1

mkdir -p /tmp/magento2/source/${EXTENSION_VENDOR}_${EXTENSION_NAME}
cp -R * /tmp/magento2/source/${EXTENSION_VENDOR}_${EXTENSION_NAME}/

pushd /tmp/magento2
composer install --prefer-dist --optimize-autoloader
test -f bin/magento || exit 1

composer config repositories.${COMPOSER_NAME} path source/${EXTENSION_VENDOR}_${EXTENSION_NAME}/
composer require ${COMPOSER_NAME}:@dev
popd

cp -R .magento/* /tmp/magento2/

set -e
pushd /tmp/magento2

if [ $TEST_SUITE == 'unit' ]; then
    echo "Prepare for running unit tests"
    source ${TRAVIS_BUILD_DIR}/.ci-scripts/scripts/unit-tests.sh
    vendor/bin/phpunit -c .magento/dev/tests/unit/phpunit.xml;
fi

if [ $TEST_SUITE == 'static' ]; then
    composer require --dev phpstan/phpstan fooman/phpstan-magento2-magic-methods;
    vendor/bin/phpstan analyse -l 2 app/code/* -a dev/tests/api-functional/framework/autoload.php;
    vendor/bin/phpcs --standard=dev/tests/static/framework/Magento/ app/code/*;
fi

if [ $TEST_SUITE == 'integration' ]; then
    source ${TRAVIS_BUILD_DIR}/.ci-scripts/scripts/integration-tests.sh
fi

if [ $TEST_SUITE == 'functional' ]; then
    source ${TRAVIS_BUILD_DIR}/.ci-scripts/scripts/functional-tests.sh
fi

if [ $TEST_SUITE == 'installation' ]; then
    source ${TRAVIS_BUILD_DIR}/.ci-scripts/scripts/installation.sh
fi

popd
