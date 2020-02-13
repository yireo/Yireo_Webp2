#!/bin/bash
if [[ ${TEST_SUITE} != "unit" ]]; then
  echo > ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/xdebug.ini
fi

echo 'memory_limit = -1' >> ~/.phpenv/versions/$(phpenv version-name)/etc/conf.d/travis.ini

phpenv rehash
phpenv config-rm xdebug.ini
