<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\ImageFactory;
use Yireo\Webp2\Convertor\Convertor;

class ImageConversionTest extends Common
{
    /**
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     * @magentoConfigFixture current_store yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsImageWithCustomStyle()
    {
        $images = $this->fixtureImageFiles();
        $imagePath = $images[0];
        $image = $this->_objectManager->get(ImageFactory::class)->createFromPath($imagePath);
        $convertor = $this->_objectManager->get(Convertor::class);
        $convertedImage = $convertor->convertImage($image);
        $this->assertNotEmpty($convertedImage->getPath());
    }

    /**
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/target_directory same_as_source
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/hash 0
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     */
    public function testIfJpegAndPngImagesAreConvertedToWebp()
    {
        foreach ($this->fixtureImageFiles() as $imagePath) {
            $image = $this->_objectManager->get(ImageFactory::class)->createFromPath($imagePath);
            $convertedImage = $this->_objectManager->get(Convertor::class)->convertImage($image);

            $expectedPath = preg_replace('/\.(jpg|png)$/', '.webp', $imagePath);
            $this->assertSame($expectedPath, $convertedImage->getPath());
            $this->assertFileExists($convertedImage->getPath());
            $this->assertStringEndsWith('.webp', $convertedImage->getUrl());
        }
    }

    /**
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 0
     */
    public function testIfConversionFailsWhenDisabled()
    {
        $images = $this->fixtureImageFiles();
        $image = $this->_objectManager->get(ImageFactory::class)->createFromPath($images[0]);

        $this->expectException(ConvertorException::class);
        $this->expectExceptionMessage('WebP conversion is not enabled');
        $this->_objectManager->get(Convertor::class)->convertImage($image);
    }

    /**
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 0
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     */
    public function testIfConversionFailsWhenImageCreationIsNotAllowed()
    {
        $images = $this->fixtureImageFiles();
        $image = $this->_objectManager->get(ImageFactory::class)->createFromPath($images[0]);

        $this->expectException(ConvertorException::class);
        $this->_objectManager->get(Convertor::class)->convertImage($image);
    }
}
