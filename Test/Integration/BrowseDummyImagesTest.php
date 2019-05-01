<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\TestFramework\TestCase\AbstractController;
use Yireo\Webp2\Test\ImageProvider;

/**
 * Class BrowseDummyImagesTest
 * @package Yireo\Webp2\Test\Integration
 */
class BrowseDummyImagesTest extends AbstractController
{
    /**
     * @magentoAdminConfigFixture system/yireo_webp/enabled 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        $this->dispatch('webp/test/images/case/multiple');
        $body = $this->getResponse()->getBody();

        $this->assertContains('type="image/webp"', $body);
        $this->assertImageTagsExist($body);
    }

    /**
     * @param string $body
     */
    private function assertImageTagsExist(string $body)
    {
        $images = $this->getImageProvider()->getImages();
        foreach ($images as $image) {
            $imageTag = $image;
            $this->assertContains($imageTag, $body);
        }
    }

    /**
     * @return ImageProvider
     */
    private function getImageProvider(): ImageProvider
    {
        return $this->_objectManager->get(ImageProvider::class);
    }
}
