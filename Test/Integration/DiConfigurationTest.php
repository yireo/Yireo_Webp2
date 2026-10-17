<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\Interception\PluginListInterface;
use Magento\Framework\View\Element\Template;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Block\Adminhtml\System\Config\ModuleVersion;
use Yireo\NextGenImages\Convertor\ConvertorListing;
use Yireo\Webp2\Convertor\Convertor;
use Yireo\Webp2\Plugin\AddCspInlineScripts;

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
    public function testIfPluginIsRegisteredInFrontend()
    {
        $pluginList = Bootstrap::getObjectManager()->get(PluginListInterface::class);
        $pluginInfo = $pluginList->get(Template::class, []);
        $this->assertArrayHasKey('Yireo_Webp2::addCspInlineScripts', $pluginInfo);
        $this->assertSame(AddCspInlineScripts::class, $pluginInfo['Yireo_Webp2::addCspInlineScripts']['instance']);
    }
}
