<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Functional;

use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleList;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class ModuleTest
 * @package Yireo\Webp2\Test\Functional
 */
class ModuleTest extends TestCase
{
    /**
     * Test if the module is registered
     */
    public function testIfModuleIsRegistered()
    {
        $registrar = new ComponentRegistrar();
        $paths = $registrar->getPaths(ComponentRegistrar::MODULE);
        $this->assertArrayHasKey('Yireo_Webp2', $paths);
    }

    /**
     * Test if the module is known and enabled
     */
    public function testIfModuleIsKnownAndEnabled()
    {
        $objectManager = Bootstrap::getObjectManager();
        $moduleList = $objectManager->create(ModuleList::class);
        $this->assertTrue($moduleList->has('Yireo_Webp2'));
    }
}
