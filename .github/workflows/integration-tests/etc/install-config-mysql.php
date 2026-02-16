<?php // phpcs:ignoreFile

use Yireo\IntegrationTestHelper\Utilities\DisableModules;
use Yireo\IntegrationTestHelper\Utilities\InstallConfig;

$disableModules = (new DisableModules(__DIR__.'/../../../../'))
    ->disableAll()
    ->enableMagento()
    ->disableByPattern('SampleData')
    ->disableByPattern('Magento_TestModule')
    ->disableMagentoInventory()
    ->disableGraphQl()
    ->enableByMagentoModuleEnv();

return (new InstallConfig())
    ->setDisableModules($disableModules)
    ->addDb('mysql', 'magento2', 'magento2', 'magento2')
    ->addRedis('redis')
    ->addElasticSearch('opensearch', 'opensearch', 9200)
    ->get();
