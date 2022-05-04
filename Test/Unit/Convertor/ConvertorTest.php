<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\TargetImageFactory;
use Yireo\NextGenImages\Util\File;
use Yireo\NextGenImages\Image\Image;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Convertor\Convertor;
use Yireo\Webp2\Convertor\ConvertWrapper;

class ConvertorTest extends TestCase
{
    /**
     * Test for Yireo\Webp2\Convertor\Convertor::getImage
     */
    public function testGetImage()
    {
        $config = $this->createMock(Config::class);
        $config->method('enabled')->willReturn(true);
        $convertor = $this->getConvertor($config);

        $this->expectException(ConvertorException::class);
        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg');
        $this->assertEquals('/test/foobar.webp', $convertor->convertImage($image));
    }

    /**
     * Test for Yireo\Webp2\Convertor\Convertor::convertImage
     */
    public function testConvert()
    {
        $convertor = $this->getConvertor();
        $this->expectException(ConvertorException::class);
        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg');
        $convertor->convertImage($image);
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

        $file = $this->createMock(File::class);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $targetImageFactory = $this->createMock(TargetImageFactory::class);

        return new Convertor(
            $config,
            $file,
            $convertWrapper,
            $targetImageFactory
        );
    }
}
