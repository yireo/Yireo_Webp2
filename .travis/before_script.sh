#!/usr/bin/env bash

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# prepare for test suite
if [[ ${TEST_SUITE} = "unit" ]]; then
    echo "Prepare for running unit tests"
    source .travis/scripts/prepare-unit-tests.sh
fi

if [[ ${TEST_SUITE} = "integration" ]]; then
    echo "Prepare for running integration tests"
    cp -R .magento/dev/* /tmp/magento2/dev/
    source .travis/scripts/prepare-integration-tests.sh
fi
