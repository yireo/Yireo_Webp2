#!/usr/bin/env bash

find "${TRAVIS_BUILD_DIR}/magento2/var/report" -type f -exec cat {} \;
cat "${TRAVIS_BUILD_DIR}/magento2/var/log/exception.log"
cat "${TRAVIS_BUILD_DIR}/magento2/var/log/system.log"
