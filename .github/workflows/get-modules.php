<?php
declare(strict_types=1);

// phpcs:ignoreFile

require 'vendor/autoload.php';
use Magento\Framework\Component\ComponentRegistrar;
$moduleNames = array_keys((new ComponentRegistrar)->getPaths('module'));
$disableModules = [];

// @todo PayPal, LoginAsCustomer, Swissup, Magento_SampleData, newrelic
if (in_array('disable-core=true', $argv)) {
    foreach ($moduleNames as $moduleName) {
        $matches = ['paypal', 'swissup', 'newrelic', 'loginascustomer'];
        foreach ($matches as $match) {
            if (stristr($moduleName, $match)) {
                $disableModules[] = $moduleName;
            }
        }
    }
}

if (in_array('disable-sample-data=true', $argv)) {
    foreach ($moduleNames as $moduleName) {
        if (stristr($moduleName, 'sampledata')) {
            $disableModules[] = $moduleName;
        }
    }
}

if (in_array('disable-adobe=true', $argv)) {
    foreach ($moduleNames as $moduleName) {
        if (stristr($moduleName, 'adobe')) {
            $disableModules[] = $moduleName;
        }
    }
}

if (in_array('disable-graphql=true', $argv)) {
    foreach ($moduleNames as $moduleName) {
        if (stristr($moduleName, 'graphql')) {
            $disableModules[] = $moduleName;
        }
    }
}

if (in_array('disable-inventory=true', $argv)) {
    foreach ($moduleNames as $moduleName) {
        if (stristr($moduleName, '_inventory')) {
            $disableModules[] = $moduleName;
        }
    }
}

echo implode(',', $disableModules).PHP_EOL;

