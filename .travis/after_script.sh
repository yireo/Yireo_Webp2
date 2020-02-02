#!/usr/bin/env bash

find "/tmp/magento2/var/report" -type f -exec cat {} \;
cat "/tmp/magento2/var/log/exception.log"
cat "/tmp/magento2/var/log/system.log"
