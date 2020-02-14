#!/usr/bin/env bash
pushd /data/magento2

test -f dev/tests/unit/phpunit.xml || exit 0

echo "Running unit tests"
cd dev/tests/unit/
../../../vendor/bin/phpunit -c phpunit.xml

popd
    
