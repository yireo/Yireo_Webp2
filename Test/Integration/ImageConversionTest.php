<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Yireo\NextGenImages\Image\ImageFactory;
use Yireo\Webp2\Convertor\Convertor;

class ImageConversionTest extends Common
{
    /**
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @@magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     * @@magentoConfigFixture current_store yireo_webp2/settings/debug 1
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
}
