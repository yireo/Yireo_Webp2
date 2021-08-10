<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\File\Read;
use Magento\Framework\Filesystem\File\ReadFactory;
use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\File;
use Yireo\NextGenImages\Image\SourceImageFactory;
use Yireo\NextGenImages\Logger\Debugger;
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
        $convertor = $this->getConvertor($config);

        $this->expectException(ConvertorException::class);
        $this->assertEquals('/test/foobar.webp', $convertor->getSourceImage('/test/foobar.jpg'));
    }

    /**
     * Test for Yireo\Webp2\Convertor\Convertor::convert
     */
    public function testConvert()
    {
        $convertor = $this->getConvertor();
        $this->expectException(ConvertorException::class);
        $convertor->convert('/images/test.jpg', '/images/test.webp');
    }

    /**
     * @param Config|null $config
     * @return Convertor
     */
    private function getConvertor(?Config $config = null): Convertor
    {
        if (!$config) {
            $config = $this->createMock(Config::class);
        }

        $sourceImageFactory = $this->createMock(SourceImageFactory::class);
        $file = $this->createMock(File::class);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $fileReadFactory = $this->getFileReadFactory();
        $debugger = $this->createMock(Debugger::class);
        $fileDriver = $this->createMock(FileDriver::class);

        return new Convertor(
            $config,
            $sourceImageFactory,
            $file,
            $convertWrapper,
            $fileReadFactory,
            $debugger,
            $fileDriver
        );
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
