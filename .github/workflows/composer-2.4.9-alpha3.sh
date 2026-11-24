#!/bin/bash
cp composer.json composer-old.json
curl -fsSL https://raw.githubusercontent.com/LokiCheckout/magento-2.4.9-patches/d670eb0a2361a9e9173262344f03398091840ec5/core/patches.json -o loki-checkout-patches.json
jq -s add composer-old.json loki-checkout-patches.json > composer.json

