#!/usr/bin/env bash
#
# Travis installation script run before running main script
#

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

if [[ ${TEST_SUITE} == "integration" ]]; then
    source .travis/scripts/mock-mail.sh
fi

# disable xdebug and adjust memory limit
if [[ ${TEST_SUITE} != "unit" ]]; then
  echo > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
fi

echo 'memory_limit = -1' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini

phpenv rehash

if [ -n "$MAGENTO_USERNAME" ]; then
    composer config --global http-basic.repo.magento.com "$MAGENTO_USERNAME" "$MAGENTO_PASSWORD"
fi

git clone --single-branch --branch ${MAGENTO_VERSION} https://github.com/magento/magento2 /tmp/magento2
test -f /tmp/magento2/composer.json || exit 1

echo "Reset root password to root"
echo "USE mysql;\nUPDATE user SET password=PASSWORD('root') WHERE user='root';\nFLUSH PRIVILEGES;\n" | mysql -u root

source .module.ini
test -z "${COMPOSER_NAME}" && exit 1
MODULE_FOLDER=`pwd`

#mkdir -p /tmp/magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}
#cp -R ${MODULE_FOLDER}/* /tmp/magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}/

pushd /tmp/magento2
composer install --dev --prefer-dist --optimize-autoloader
test -f bin/magento || exit 1

composer config repositories.${COMPOSER_NAME} path ${MODULE_FOLDER}
composer require ${COMPOSER_NAME}:@dev
popd

cp -R .magento/* /tmp/magento2/
