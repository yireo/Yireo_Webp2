<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class MultipleImagesTest extends Common
{
    /**
     * @magentoAdminConfigFixture yireo_nextgenimages/settings/enabled 1
     * @magentoAdminConfigFixture yireo_nextgenimages/settings/convert_images 1
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsWebpImages(): void
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_images');
        $this->dispatch('webp/test/images');
        $this->assertSame('multiple_images', $this->getRequest()->getParam('case'));
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = $this->getResponse()->getBody();
        $this->assertImageTagsExist($body, [$this->getImageProvider()->getImage()]);
        $this->assertTrue((bool)strpos($body, 'type="image/webp"'));

        if (!getenv('TRAVIS')) {
            $this->assertImageTagsExist($body, $this->getImageProvider()->getImages());
        }
    }
}
