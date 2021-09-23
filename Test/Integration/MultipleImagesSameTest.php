<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class MultipleImagesSameTest extends Common
{
    /**
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsSingleWebpImage()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_images_same');
        $this->dispatch('webp/test/images');
        $this->assertSame('multiple_images_same', $this->getRequest()->getParam('case'));
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = $this->getResponse()->getContent();
        $this->assertTrue((bool)strpos($body, 'type="image/webp"'));

        if (!getenv('TRAVIS')) {
            $this->assertImageTagsExist($body, [$this->getImageProvider()->getImage()]);
        }
    }
}
