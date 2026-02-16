<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use PHPUnit\Framework\TestCase;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\TargetImageFactory;
use Yireo\NextGenImages\Util\File;
use Yireo\NextGenImages\Image\Image;
use Yireo\NextGenImages\Image\ImageFactory;
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
     * Test for Yireo\Webp2\Convertor\Convertor::convertImage
     */
    public function testItWillConvertSrcSetIfFound()
    {
        $givenSrcSet = '/test/foobar-small.jpg 500w, /test/foobar-medium.jpg 800w, /test/foobar-default.jpg';
        $expectedSrcSet  = '/test/foobar-small.webp 500w, /test/foobar-medium.webp 800w, /test/foobar-default.webp ';
        
        $config = $this->createMock(Config::class);
        $config->method('enabled')->willReturn(true);
        $config->method('allowImageCreation')->willReturn(true);
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        
        $webpImageFile = $this->createMock(Image::class);
        $webpImageFile->method('getUrl')->willReturnOnConsecutiveCalls(
            '/test/foobar-small.webp',
            '/test/foobar-medium.webp',
            '/test/foobar-default.webp',
        );
        $webpImageFile->expects($this->once())
            ->method('setSrcSet')
            ->with($expectedSrcSet)
            ->willReturnSelf();
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $targetImageFactory = $this->createMock(TargetImageFactory::class);
        $targetImageFactory->method('create')->willReturn($webpImageFile);
        $imageFactory = $this->createMock(ImageFactory::class);
        
        $convertor = new Convertor(
            $config,
            $imageFile,
            $convertWrapper,
            $targetImageFactory,
            $imageFactory
        );
        
        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg', $givenSrcSet);
        $convertedImage = $convertor->convertImage($image);
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
        $imageFactory = $this->createMock(ImageFactory::class);
        
        return new Convertor(
            $config,
            $file,
            $convertWrapper,
            $targetImageFactory,
            $imageFactory
        );
    }
}
