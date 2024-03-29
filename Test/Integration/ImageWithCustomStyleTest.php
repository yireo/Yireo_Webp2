<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class ImageWithCustomStyleTest extends Common
{
    /**
     * @magentoAppArea frontend
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoCache all disabled
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @@magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     * @@magentoConfigFixture current_store dev/static/sign 0
     */
    public function testIfHtmlContainsImageWithCustomStyle()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'image_with_custom_style');
        $this->dispatch('webp/test/images');
        $this->assertSame('image_with_custom_style', $this->getRequest()->getParam('case'));
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = $this->getResponse()->getContent();
        $this->assertImageTagsExist($body, [$this->getImageProvider()->getImage()]);
        $this->assertTrue((bool)strpos($body, 'type="image/webp"'));

        if (!getenv('TRAVIS')) {
            $this->assertTrue((bool)strpos($body, 'style="display:insane; opacity:666;"'));
        }
    }
}
