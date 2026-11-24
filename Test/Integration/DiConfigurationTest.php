<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Block\Adminhtml\System\Config\ModuleVersion;
use Yireo\NextGenImages\Convertor\ConvertorListing;
use Yireo\Webp2\Convertor\Convertor;

class DiConfigurationTest extends TestCase
{
    public function testIfConvertorIsAddedToConvertorListing()
    {
        $convertorListing = Bootstrap::getObjectManager()->create(ConvertorListing::class);
        $webpConvertors = array_filter(
            $convertorListing->getConvertors(),
            fn ($convertor) => $convertor instanceof Convertor
        );
        $this->assertCount(1, $webpConvertors);
    }

    public function testIfModuleVersionVirtualTypeCanBeCreated()
    {
        $block = Bootstrap::getObjectManager()->create(
            'Yireo\Webp2\VirtualType\Block\Adminhtml\System\Config\ModuleVersion'
        );
        $this->assertInstanceOf(ModuleVersion::class, $block);
    }

    /**
     * @magentoAppArea frontend
     */
    public function testIfNoPluginIsRegisteredOnEveryTemplateBlock()
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginListInterface::class);
        $pluginInfo = (array)$pluginList->get(Template::class, []);
        foreach (array_keys($pluginInfo) as $pluginName) {
            $this->assertStringStartsNotWith('Yireo_Webp2::', (string)$pluginName);
        }
    }
}
