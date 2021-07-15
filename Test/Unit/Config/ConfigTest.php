<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2\Config\Config;

class ConfigTest extends TestCase
{
    public function testEnabled()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $config = new Config($scopeConfig, $storeManager);
        $this->assertFalse($config->enabled());

        $scopeConfig->method('getValue')->willReturn(1);
        $this->assertTrue($config->enabled());
    }

    public function testGetQualityLevel()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $config = new Config($scopeConfig, $storeManager);
        $this->assertEquals(1, $config->getQualityLevel());

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $config = new Config($scopeConfig, $storeManager);
        $scopeConfig->method('getValue')->willReturn(42);
        $this->assertEquals(42, $config->getQualityLevel());

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(142);
        $config = new Config($scopeConfig, $storeManager);
        $this->assertEquals(100, $config->getQualityLevel());

        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn(0);
        $config = new Config($scopeConfig, $storeManager);
        $this->assertEquals(1, $config->getQualityLevel());
    }

    public function testGetConvertors()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $config = new Config($scopeConfig, $storeManager);
        $this->assertNotEmpty($config->getConvertors());
    }
}
