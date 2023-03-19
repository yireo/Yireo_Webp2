<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Config\Config as NextGenImagesConfig;
use Yireo\Webp2\Config\Config;

class ConfigTest extends TestCase
{
    public function testEnabled()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $nextGenImagesConfig = $this->createMock(NextGenImagesConfig::class);
        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertFalse($config->enabled());

        $scopeConfig->method('getValue')->willReturn(1);
        $this->assertTrue($config->enabled());
    }

    public function testGetQualityLevel()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $nextGenImagesConfig = $this->createMock(NextGenImagesConfig::class);
        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertEquals(1, $config->getQualityLevel());

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(42);
        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertEquals(42, $config->getQualityLevel());

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(142);
        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertEquals(100, $config->getQualityLevel());

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(0);
        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertEquals(1, $config->getQualityLevel());
    }

    public function testGetConvertors()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $nextGenImagesConfig = $this->createMock(NextGenImagesConfig::class);
        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertNotEmpty($config->getConvertors());
    }
}
