<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\View\LayoutInterface;

/**
 * Class BrowseDummyImagesTest
 * @package Yireo\Webp2\Test\Integration
 */
class BrowseDummyImagesTest extends Common
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    /*
    public function testIfHtmlContainsWebpImages()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_images');
        $this->dispatch('webp/test/images');
        $this->assertSame('multiple_images', $this->getRequest()->getParam('case'));

        $body = $this->getResponse()->getBody();
        $this->assertImageTagsExist($body, $this->getImageProvider()->getImages());
    }
    */

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsSingleWebpImage()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_images_same');
        $this->dispatch('webp/test/images');
        $this->assertSame('multiple_images_same', $this->getRequest()->getParam('case'));

        $body = $this->getResponse()->getBody();
        $this->assertImageTagsExist($body, [$this->getImageProvider()->getImage()]);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsImageWithCustomStyle()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'image_with_custom_style');
        $this->dispatch('webp/test/images');
        $this->assertSame('image_with_custom_style', $this->getRequest()->getParam('case'));

        $body = $this->getResponse()->getBody();
        $this->assertImageTagsExist($body, [$this->getImageProvider()->getImage()]);
        $this->assertContains('style="display:insane; opacity:666;"', $body);
    }

    /**
     * @param string $body
     */
    private function assertImageTagsExist(string $body, $images)
    {
        foreach ($images as $image) {
            $webPImage = preg_replace('/\.(png|jpg)$/', '.webp', $image);
            $this->assertContains($webPImage, $body);
        }
    }
}
