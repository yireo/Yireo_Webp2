<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class ImageWithCustomStyleTest extends Common
{
    /**
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
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
