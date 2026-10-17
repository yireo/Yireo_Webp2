<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class DisabledTest extends Common
{
    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 0
     */
    public function testIfHtmlContainsNoWebpImagesWhenDisabled()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_images');
        $this->dispatch('webp/test/images');
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = (string)$this->getResponse()->getBody();
        foreach ($this->getImageProvider()->getImages() as $image) {
            $this->assertStringContainsString(basename($image), $body);
        }

        $this->assertStringNotContainsString('type="image/webp"', $body);
        $this->assertStringNotContainsString('.webp', $body);
    }
}
