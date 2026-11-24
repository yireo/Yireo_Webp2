<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Config\Config as NextGenImagesConfig;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Model\Config\Source\Convertors;

class ConvertorsTest extends TestCase
{
    public function testToOptionArray()
    {
        $options = (new Convertors())->toOptionArray();
        $this->assertSame(['cwebp', 'gd', 'imagick'], array_column($options, 'value'));

        foreach ($options as $option) {
            $this->assertNotEmpty($option['label']);
        }
    }

    public function testSelectedOptionsAreAcceptedByConfig()
    {
        $values = array_column((new Convertors())->toOptionArray(), 'value');
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(implode(',', $values));
        $config = new Config(
            $scopeConfig,
            $this->createMock(StoreManagerInterface::class),
            $this->createMock(NextGenImagesConfig::class)
        );

        $this->assertSame($values, $config->getConvertors());
    }
}
