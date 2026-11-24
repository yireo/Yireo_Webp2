#!/bin/bash
cp composer.json composer-old.json
curl -fsSL https://raw.githubusercontent.com/LokiCheckout/magento-2.4.5-patches/3848e73173a7400a5687f323804da8bc611a7231/modules/patches.json -o loki-checkout-patches.json
jq -s add composer-old.json loki-checkout-patches.json > composer.json

