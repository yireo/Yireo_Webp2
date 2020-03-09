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
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        $this->fixtureImageFiles();

        $this->getResponse()->clearBody();
        $this->getResponse()->setHeader('Accept', 'image/webp');
        $this->dispatch('webp/test/images/case/multiple_images');

        /** @var LayoutInterface $layout */
        $layout = $this->_objectManager->get(LayoutInterface::class);
        $body = $layout->getOutput();

        $this->assertImageTagsExist($body, $this->getImageProvider()->getImages());
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsSingleWebpImage()
    {
        $this->fixtureImageFiles();

        $this->getResponse()->clearBody();
        $this->getResponse()->setHeader('Accept', 'image/webp');
        $this->dispatch('webp/test/images/case/multiple_images_same');

        /** @var LayoutInterface $layout */
        $layout = $this->_objectManager->get(LayoutInterface::class);
        $body = $layout->getOutput();

        $this->assertImageTagsExist($body, [$this->getImageProvider()->getImage()]);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsImageWithCustomStyle()
    {
        $this->fixtureImageFiles();

        foreach ($this->getLayout()->getUpdate()->getHandles() as $handle) {
            $this->getLayout()->getUpdate()->removeHandle($handle);
        }

        $this->getResponse()->clearBody();
        $this->getResponse()->setHeader('Accept', 'image/webp');
        $this->dispatch('webp/test/images/case/image_with_custom_style');

        /** @var LayoutInterface $layout */
        $body = $this->getLayout()->getOutput();

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

    /**
     * @return LayoutInterface
     */
    private function getLayout(): LayoutInterface
    {
        return $this->_objectManager->get(LayoutInterface::class);
    }
}
