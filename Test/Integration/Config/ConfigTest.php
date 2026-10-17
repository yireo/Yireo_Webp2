<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Config;

use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2\Config\Config;

class ConfigTest extends TestCase
{
    public function testDefaultValues()
    {
        $config = Bootstrap::getObjectManager()->create(Config::class);
        $this->assertTrue($config->enabled());
        $this->assertSame(80, $config->getQualityLevel());
        $this->assertSame(['cwebp', 'gd', 'imagick', 'wpc', 'ewww'], $config->getConvertors());
        $this->assertSame('lossy', $config->getEncoding());
    }

    /**
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 0
     * @magentoConfigFixture current_store yireo_webp2/settings/quality_level 55
     * @magentoConfigFixture current_store yireo_webp2/settings/convertors gd,imagick
     * @magentoConfigFixture current_store yireo_webp2/settings/encoding lossless
     */
    public function testStoreValues()
    {
        $config = Bootstrap::getObjectManager()->create(Config::class);
        $this->assertFalse($config->enabled());
        $this->assertSame(55, $config->getQualityLevel());
        $this->assertSame(['gd', 'imagick'], $config->getConvertors());
        $this->assertSame('lossless', $config->getEncoding());
    }

    /**
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 0
     */
    public function testAllowImageCreationFollowsNextGenImages()
    {
        $config = Bootstrap::getObjectManager()->create(Config::class);
        $this->assertFalse($config->allowImageCreation());
    }
}
