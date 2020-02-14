#!/usr/bin/env bash

find /data/magento2/var/report -type f -exec cat {} \;
test -f /data/magento2/var/log/exception.log && cat /data/magento2/var/log/exception.log
test -f /data/magento2/var/log/system.log && cat /data/magento2/var/log/system.log
