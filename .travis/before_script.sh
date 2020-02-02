#!/usr/bin/env bash

set -e
trap '>&2 echo Error: Command \`$BASH_COMMAND\` on line $LINENO failed with exit code $?' ERR

# prepare for test suite
pushd magento2
if [[ ${TEST_SUITE} = "unit" ]]; then
    echo "Prepare for running unit tests"
    source ./scripts/prepare-unit-tests.sh
fi

if [[ ${TEST_SUITE} = "integration" ]]; then
    echo "Prepare for running integration tests"
    source ./scripts/prepare-integration-tests.sh
fi
popd
