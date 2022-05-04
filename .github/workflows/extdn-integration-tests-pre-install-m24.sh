#!/bin/bash
composer config minimum-stability dev
composer config prefer-stable false

composer require yireo/magento2-replace-bundled:^4.0 --no-update
