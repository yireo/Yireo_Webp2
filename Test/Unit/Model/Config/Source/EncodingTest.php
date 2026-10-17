<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Model\Config\Source;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Config\Config as NextGenImagesConfig;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Model\Config\Source\Encoding;

class EncodingTest extends TestCase
{
    public function testToOptionArray()
    {
        $options = (new Encoding())->toOptionArray();
        $this->assertSame(['lossy', 'lossless', 'auto'], array_column($options, 'value'));

        foreach ($options as $option) {
            $this->assertNotEmpty($option['label']);
        }
    }

    public function testEveryOptionIsAcceptedByConfig()
    {
        foreach ((new Encoding())->toOptionArray() as $option) {
            $scopeConfig = $this->createMock(ScopeConfigInterface::class);
            $scopeConfig->method('getValue')->willReturn($option['value']);
            $config = new Config(
                $scopeConfig,
                $this->createMock(StoreManagerInterface::class),
                $this->createMock(NextGenImagesConfig::class)
            );

            $this->assertSame($option['value'], $config->getEncoding());
        }
    }
}
