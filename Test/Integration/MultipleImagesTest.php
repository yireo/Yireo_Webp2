<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

/**
 * Class MultipleImagesTest
 * @package Yireo\Webp2\Test\Integration
 */
class MultipleImagesTest extends Common
{
    /**
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoAdminConfigFixture yireo_webp2/settings/debug 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_images');
        $this->dispatch('webp/test/images');
        $this->assertSame('multiple_images', $this->getRequest()->getParam('case'));
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = $this->getResponse()->getBody();
        $this->assertContains('type="image/webp"', $body);

        if (!getenv('TRAVIS')) {
            $this->assertImageTagsExist($body, $this->getImageProvider()->getImages());
        }
    }
}
