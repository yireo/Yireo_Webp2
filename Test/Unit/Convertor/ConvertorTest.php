<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use Magento\Framework\Filesystem\File\Read;
use Magento\Framework\Filesystem\File\ReadFactory;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\File;
use Yireo\NextGenImages\Image\SourceImageFactory;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Convertor\Convertor;
use Yireo\Webp2\Image\ConvertWrapper;

class ConvertorTest extends TestCase
{
    /**
     * Test for Yireo\Webp2\Convertor\Convertor::getSourceImage
     */
    public function testGetSourceImage()
    {
        $config = $this->createMock(Config::class);
        $config->method('enabled')->willReturn(true);

        $sourceImageFactory = $this->createMock(SourceImageFactory::class);
        $file = $this->createMock(File::class);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $fileReadFactory = $this->getFileReadFactory();

        $convertor = new Convertor($config, $sourceImageFactory, $file, $convertWrapper, $fileReadFactory);

        $this->expectException(ConvertorException::class);
        $this->assertEquals('/test/foobar.webp', $convertor->getSourceImage('/test/foobar.jpg'));
    }

    /**
     * Test for Yireo\Webp2\Convertor\Convertor::convert
     */
    public function testConvert()
    {
        $config = $this->createMock(Config::class);
        $sourceImageFactory = $this->createMock(SourceImageFactory::class);
        $file = $this->createMock(File::class);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $fileReadFactory = $this->getFileReadFactory();

        $convertor = new Convertor($config, $sourceImageFactory, $file, $convertWrapper, $fileReadFactory);
        $this->assertFalse($convertor->convert('/images/test.jpg', '/images/test.webp'));
    }

    /**
     * Test for Yireo\Webp2\Convertor\Convertor::urlExists
     */
    public function testUrlExists()
    {
        $config = $this->createMock(Config::class);
        $sourceImageFactory = $this->createMock(SourceImageFactory::class);
        $file = $this->createMock(File::class);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $fileReadFactory = $this->getFileReadFactory();

        $convertor = new Convertor($config, $sourceImageFactory, $file, $convertWrapper, $fileReadFactory);
        $this->assertFalse($convertor->urlExists('http://localhost/test.webp'));
    }

    /**
     * @return ReadFactory
     */
    private function getFileReadFactory(): ReadFactory
    {
        $fileRead = $this->createMock(Read::class);
        $fileReadFactory = $this->createMock(ReadFactory::class);
        $fileReadFactory->method('create')->willReturn($fileRead);
        return $fileReadFactory;
    }
}
