#!/usr/bin/env bash
pushd dev/tests/unit
cp -f ${TRAVIS_BUILD_DIR}/phpunit-yireo.xml phpunit.xml
popd

composer require "mustache/mustache":"~2.5"
composer require "php-coveralls/php-coveralls":"^1.0"
