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

git clone --depth 1 https://github.com/magento/magento2:${MAGENTO_VERSION} /tmp/magento2

source .module.ini
mkdir -p /tmp/magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}
cp -R * /tmp/magento2/app/code/${EXTENSION_VENDOR}/${EXTENSION_NAME}/

pushd /tmp/magento2
composer install --prefer-dist
test -f bin/magento || exit 1
popd

