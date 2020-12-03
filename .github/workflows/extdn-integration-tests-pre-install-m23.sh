#!/bin/bash
composer config minimum-stability dev
composer config prefer-stable false

composer require yireo/magento2-replace-bundled:^3.0 --no-update
composer require yireo/magento2-replace-test:@dev --no-update
