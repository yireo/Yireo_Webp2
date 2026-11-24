<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\View\Asset\File\NotFoundException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WebPConvert\Convert\Exceptions\ConversionFailed\InvalidInput\InvalidImageTypeException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\TargetImageFactory;
use Yireo\NextGenImages\Util\File;
use Yireo\NextGenImages\Image\Image;
use Yireo\NextGenImages\Image\ImageFactory;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Convertor\Convertor;
use Yireo\Webp2\Convertor\ConvertWrapper;
use Yireo\Webp2\Exception\InvalidConvertorException;

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

    public function testConvertImageThrowsExceptionWhenDisabled()
    {
        $config = $this->getConfigMock(false);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->never())->method('convert');
        $convertor = $this->getConvertor($config, null, $convertWrapper);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('WebP conversion is not enabled');
        $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
    }

    /**
     * @dataProvider getUnsupportedImages
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getUnsupportedImages')]
    public function testConvertImageThrowsExceptionOnUnsupportedMimetype(string $path, string $mimetype)
    {
        $convertor = $this->getConvertor($this->getConfigMock());

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('The mimetype "' . $mimetype . '" is not supported');
        $convertor->convertImage(new Image($path, '/test/' . basename($path)));
    }

    public static function getUnsupportedImages(): array
    {
        return [
            ['/tmp/pub/test/foobar.gif', 'image/gif'],
            ['/tmp/pub/test/foobar.webp', 'image/webp'],
        ];
    }

    public function testConvertImageThrowsExceptionWhenSourceDoesNotExist()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(false);
        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('Source cached image does not exists: /tmp/pub/test/foobar.jpg');
        $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
    }

    public function testConvertImageSkipsConversionWhenNotNeeded()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(false);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->never())->method('convert');
        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->expects($this->never())->method('createFromUrl');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper, null, $imageFactory);
        $webpImage = $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));

        $this->assertSame('/tmp/pub/test/foobar.webp', $webpImage->getPath());
        $this->assertSame('/test/foobar.webp', $webpImage->getUrl());
        $this->assertNull($webpImage->getSrcSet());
    }

    public function testConvertImageCallsConvertWrapperWithSourceAndTargetPath()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->once())
            ->method('convert')
            ->with('/tmp/pub/test/foobar.jpg', '/tmp/pub/test/foobar.webp');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);
        $webpImage = $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
        $this->assertSame('/test/foobar.webp', $webpImage->getUrl());
    }

    public function testConvertImageThrowsExceptionWhenImageCreationIsNotAllowed()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->never())->method('convert');

        $convertor = $this->getConvertor($this->getConfigMock(true, false), $imageFile, $convertWrapper);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('WebP conversion is not enabled');
        $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
    }

    public function testConvertImageThrowsExceptionWhenInvalidImageLeavesNoTarget()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturnCallback(function (string $path) {
            return $path === '/tmp/pub/test/foobar.jpg';
        });
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->method('convert')->willThrowException(new InvalidImageTypeException('Invalid'));

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('WebP path "/tmp/pub/test/foobar.webp" does not exist after conversion');
        $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
    }

    public function testConvertImageThrowsExceptionWhenAllConvertorsFailSilently()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturnCallback(function (string $path) {
            return $path === '/tmp/pub/test/foobar.jpg';
        });
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->once())->method('convert');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('WebP path "/tmp/pub/test/foobar.webp" does not exist after conversion');
        $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
    }

    public function testConvertImageReturnsImageWhenInvalidImageStillHasTarget()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->method('convert')->willThrowException(new InvalidImageTypeException('Invalid'));

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);
        $webpImage = $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
        $this->assertSame('/tmp/pub/test/foobar.webp', $webpImage->getPath());
    }

    /**
     * @dataProvider getConversionExceptions
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getConversionExceptions')]
    public function testConvertImageWrapsConversionExceptions(string $exceptionClass)
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->method('convert')->willThrowException(new $exceptionClass('Something failed'));

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('/tmp/pub/test/foobar.webp: Something failed');
        $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
    }

    public static function getConversionExceptions(): array
    {
        return [
            [ConversionFailedException::class],
            [InvalidConvertorException::class],
        ];
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
        $webpImageFile->method('getPath')->willReturn('/tmp/pub/test/foobar.webp');
        $webpImageFile->method('getUrl')->willReturnOnConsecutiveCalls(
            '/test/foobar-small.webp',
            '/test/foobar-medium.webp',
            '/test/foobar-default.webp',
        );
        $webpImageFile->expects($this->once())
            ->method('setSrcSet')
            ->with($expectedSrcSet);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $targetImageFactory = $this->createMock(TargetImageFactory::class);
        $targetImageFactory->method('create')->willReturn($webpImageFile);
        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->method('createFromUrl')->willReturnCallback(function (string $url) {
            return new Image('/tmp/pub' . $url, $url);
        });

        $convertor = new Convertor(
            $config,
            $imageFile,
            $convertWrapper,
            $targetImageFactory,
            $imageFactory,
            $this->getDirectoryList(),
            $this->createMock(FileDriver::class)
        );

        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg', $givenSrcSet);
        $convertor->convertImage($image);
    }

    public function testItWillConvertEachSrcSetImage()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);

        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->expects($this->exactly(2))
            ->method('createFromUrl')
            ->willReturnCallback(function (string $url) {
                return new Image('/tmp/pub' . $url, $url);
            });

        $convertedPaths = [];
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->method('convert')->willReturnCallback(
            function (string $source, string $target) use (&$convertedPaths) {
                $convertedPaths[] = $target;
            }
        );

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper, null, $imageFactory);
        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg', '/test/foobar.jpg, /test/foobar-2x.png 2x');
        $webpImage = $convertor->convertImage($image);

        $this->assertSame('/test/foobar.webp , /test/foobar-2x.webp 2x', $webpImage->getSrcSet());
        $this->assertSame([
            '/tmp/pub/test/foobar.webp',
            '/tmp/pub/test/foobar.webp',
            '/tmp/pub/test/foobar-2x.webp',
        ], $convertedPaths);
    }

    public function testItWillNotConvertSrcSetImageWithUnsupportedMimetype()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);

        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->method('createFromUrl')->willReturnCallback(function (string $url) {
            return new Image('/tmp/pub' . $url, $url);
        });

        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->once())->method('convert');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper, null, $imageFactory);
        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg', '/test/foobar.gif 2x');

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('The mimetype "image/gif" is not supported');
        $convertor->convertImage($image);
    }

    public function testItWillNotConvertSrcSetWhenEmpty()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->expects($this->never())->method('createFromUrl');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, null, null, $imageFactory);

        $webpImage = $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg'));
        $this->assertNull($webpImage->getSrcSet());

        $webpImage = $convertor->convertImage(new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg', ''));
        $this->assertNull($webpImage->getSrcSet());
    }

    /**
     * @dataProvider getMaliciousDescriptors
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getMaliciousDescriptors')]
    public function testItWillDropInvalidSrcSetDescriptors(string $srcSet, string $expectedSrcSet)
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);

        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->method('createFromUrl')->willReturnCallback(function (string $url) {
            return new Image('/tmp/pub' . $url, $url);
        });

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, null, null, $imageFactory);
        $image = new Image('/tmp/pub/test/foobar.jpg', '/test/foobar.jpg', $srcSet);

        $this->assertSame($expectedSrcSet, $convertor->convertImage($image)->getSrcSet());
    }

    public static function getMaliciousDescriptors(): array
    {
        return [
            'attribute breakout' => ['/test/a.jpg 1x"onerror="alert(1)', '/test/a.webp '],
            'tag breakout' => ['/test/a.jpg 2x"><script>alert(1)</script>', '/test/a.webp '],
            'valid width' => ['/test/a.jpg 640w', '/test/a.webp 640w'],
            'valid density' => ['/test/a.jpg 1.5x', '/test/a.webp 1.5x'],
        ];
    }

    /**
     * @dataProvider getDisallowedPaths
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('getDisallowedPaths')]
    public function testConvertImageRejectsDisallowedPaths(string $path, string $expectedMessage)
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->never())->method('convert');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage($expectedMessage);
        $convertor->convertImage(new Image($path, '/foobar.jpg'));
    }

    public static function getDisallowedPaths(): array
    {
        return [
            'traversal out of pub' => ['/tmp/pub/../../etc/foobar.jpg', 'is outside of the media and static folders'],
            'traversal via media' => ['/tmp/pub/media/../../app/foobar.jpg', 'is outside of the media and static folders'],
            'outside pub' => ['/var/www/app/etc/foobar.png', 'is outside of the media and static folders'],
            'customer media' => ['/tmp/pub/media/customer/a/b/foobar.jpg', 'is inside a protected media folder'],
            'customer address media' => ['/tmp/pub/media/customer_address/foobar.jpg', 'is inside a protected media folder'],
            'custom options media' => ['/tmp/pub/media/custom_options/quote/foobar.jpg', 'is inside a protected media folder'],
            'downloadable media' => ['/tmp/pub/media/downloadable/files/foobar.jpg', 'is inside a protected media folder'],
            'import media' => ['/tmp/pub/media/import/foobar.jpg', 'is inside a protected media folder'],
            'traversal into customer media' => ['/tmp/pub/media/catalog/../customer/foobar.jpg', 'is inside a protected media folder'],
        ];
    }

    public function testConvertImageAllowsMediaAndStaticPaths()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);
        $convertWrapper = $this->createMock(ConvertWrapper::class);
        $convertWrapper->expects($this->exactly(3))->method('convert');

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, $convertWrapper);
        $convertor->convertImage(new Image('/tmp/pub/media/catalog/product/foobar.jpg', '/foobar.jpg'));
        $convertor->convertImage(new Image('/tmp/pub/media/customer-images/foobar.jpg', '/foobar.jpg'));
        $convertor->convertImage(new Image('/tmp/pub/static/frontend/Magento/luma/foobar.png', '/foobar.png'));
    }

    public function testItWillWrapNotFoundExceptionOfSrcSetImage()
    {
        $imageFile = $this->createMock(File::class);
        $imageFile->method('fileExists')->willReturn(true);
        $imageFile->method('needsConversion')->willReturn(true);

        $imageFactory = $this->createMock(ImageFactory::class);
        $imageFactory->method('createFromUrl')
            ->willThrowException(new NotFoundException('URL is not a local file'));

        $convertor = $this->getConvertor($this->getConfigMock(), $imageFile, null, null, $imageFactory);
        $image = new Image(
            '/tmp/pub/test/foobar.jpg',
            '/test/foobar.jpg',
            '/test/foobar.jpg, https://cdn.example.com/foobar.jpg 2x'
        );

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('Unable to convert srcset image: URL is not a local file');
        $convertor->convertImage($image);
    }

    /**
     * @param bool $enabled
     * @param bool $allowImageCreation
     * @return Config&MockObject
     */
    private function getConfigMock(bool $enabled = true, bool $allowImageCreation = true): Config
    {
        $config = $this->createMock(Config::class);
        $config->method('enabled')->willReturn($enabled);
        $config->method('allowImageCreation')->willReturn($allowImageCreation);
        return $config;
    }

    /**
     * @return TargetImageFactory&MockObject
     */
    private function getTargetImageFactory(): TargetImageFactory
    {
        $targetImageFactory = $this->createMock(TargetImageFactory::class);
        $targetImageFactory->method('create')->willReturnCallback(function (Image $image, string $suffix) {
            $replace = function (string $value) use ($suffix) {
                return (string)preg_replace('/\.(jpg|jpeg|png)$/', '.' . $suffix, $value);
            };

            return new Image($replace($image->getPath()), $replace($image->getUrl()));
        });

        return $targetImageFactory;
    }

    /**
     * @param Config|null $config
     * @param File|null $file
     * @param ConvertWrapper|null $convertWrapper
     * @param TargetImageFactory|null $targetImageFactory
     * @param ImageFactory|null $imageFactory
     * @return Convertor
     */
    private function getConvertor(
        ?Config $config = null,
        ?File $file = null,
        ?ConvertWrapper $convertWrapper = null,
        ?TargetImageFactory $targetImageFactory = null,
        ?ImageFactory $imageFactory = null
    ): Convertor {
        return new Convertor(
            $config ?? $this->createMock(Config::class),
            $file ?? $this->createMock(File::class),
            $convertWrapper ?? $this->createMock(ConvertWrapper::class),
            $targetImageFactory ?? $this->getTargetImageFactory(),
            $imageFactory ?? $this->createMock(ImageFactory::class),
            $this->getDirectoryList(),
            $this->createMock(FileDriver::class)
        );
    }

    /**
     * @return DirectoryList&MockObject
     */
    private function getDirectoryList(): DirectoryList
    {
        $directoryList = $this->createMock(DirectoryList::class);
        $directoryList->method('getRoot')->willReturn('/tmp');
        $directoryList->method('getPath')->willReturnMap([
            [DirectoryList::PUB, '/tmp/pub'],
            [DirectoryList::MEDIA, '/tmp/pub/media'],
            [DirectoryList::STATIC_VIEW, '/tmp/pub/static'],
        ]);

        return $directoryList;
    }
}
