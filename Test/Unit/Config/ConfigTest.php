<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Config\Config as NextGenImagesConfig;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Exception\InvalidConvertorException;

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

    public function testEnabledReturnsFalseWhenStoreIsNotFound()
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->expects($this->never())->method('getValue');
        $storeManager = $this->createMock(StoreManagerInterface::class);
        $storeManager->method('getStore')->willThrowException(new NoSuchEntityException());
        $nextGenImagesConfig = $this->createMock(NextGenImagesConfig::class);

        $config = new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
        $this->assertFalse($config->enabled());
    }

    public function testAllowImageCreationIsDelegatedToNextGenImages()
    {
        $nextGenImagesConfig = $this->createMock(NextGenImagesConfig::class);
        $nextGenImagesConfig->method('allowImageCreation')->willReturnOnConsecutiveCalls(true, false);

        $config = $this->getConfig(null, $nextGenImagesConfig);
        $this->assertTrue($config->allowImageCreation());
        $this->assertFalse($config->allowImageCreation());
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

    public function testGetConvertorsReturnsAllConvertorsWhenEmpty()
    {
        $config = $this->getConfig('');
        $this->assertSame(['cwebp', 'gd', 'imagick', 'wpc', 'ewww'], $config->getConvertors());
    }

    public function testGetConvertorsTrimsAndSkipsEmptyValues()
    {
        $config = $this->getConfig(' gd , imagick ,');
        $this->assertSame(['gd', 'imagick'], $config->getConvertors());
    }

    public function testGetConvertorsThrowsExceptionOnInvalidConvertor()
    {
        $config = $this->getConfig('gd,foobar');
        $this->expectException(InvalidConvertorException::class);
        $this->expectExceptionMessage('Invalid convertor: "foobar"');
        $config->getConvertors();
    }

    public function testGetEncodingReturnsLossyWhenEmpty()
    {
        $config = $this->getConfig('');
        $this->assertSame('lossy', $config->getEncoding());
    }

    /**
     * @dataProvider getValidEncodings
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getValidEncodings')]
    public function testGetEncodingReturnsValidEncoding(string $encoding)
    {
        $config = $this->getConfig($encoding);
        $this->assertSame($encoding, $config->getEncoding());
    }

    public static function getValidEncodings(): array
    {
        return [
            ['lossy'],
            ['lossless'],
            ['auto'],
        ];
    }

    public function testGetEncodingThrowsExceptionOnInvalidEncoding()
    {
        $config = $this->getConfig('foobar');
        $this->expectException(InvalidConvertorException::class);
        $this->expectExceptionMessage('Invalid encoding: "foobar"');
        $config->getEncoding();
    }

    private function getConfig($value = null, ?NextGenImagesConfig $nextGenImagesConfig = null): Config
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturn($value);
        $storeManager = $this->createMock(StoreManagerInterface::class);
        if (!$nextGenImagesConfig) {
            $nextGenImagesConfig = $this->createMock(NextGenImagesConfig::class);
        }

        return new Config($scopeConfig, $storeManager, $nextGenImagesConfig);
    }
}
