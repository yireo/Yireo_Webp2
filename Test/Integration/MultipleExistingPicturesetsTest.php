<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class MultipleExistingPicturesetsTest extends Common
{
    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     */
    public function testIfExistingPictureTagsAreNotWrappedAgain()
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'multiple_existing_picturesets');
        $this->dispatch('webp/test/images');
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = (string)$this->getResponse()->getBody();
        foreach ($this->getImageProvider()->getImages() as $image) {
            $this->assertStringContainsString(basename($image), $body);
        }

        $this->assertSame(3, substr_count($body, '<picture'));
        $this->assertStringNotContainsString('type="image/webp"', $body);
    }
}
