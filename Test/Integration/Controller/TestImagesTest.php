<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Controller;

use Magento\Framework\View\LayoutInterface;
use Yireo\Webp2\Test\Integration\Common;

class TestImagesTest extends Common
{
    /**
     * @magentoAppArea frontend
     */
    public function testIfCaseParameterIsSanitized()
    {
        $this->getRequest()->setParam('case', '../Multiple_Images<script>');
        $this->dispatch('webp/test/images');
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $handles = $this->_objectManager->get(LayoutInterface::class)->getUpdate()->getHandles();
        $this->assertContains('webp_test_images_multiple_imagesscript', $handles);
        foreach ($handles as $handle) {
            $this->assertDoesNotMatchRegularExpression('/[^a-z0-9_]/', $handle);
        }
    }

    /**
     * @magentoAppArea frontend
     * @magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     */
    public function testIfUnknownImageIsNotReplaced()
    {
        $this->getRequest()->setParam('case', 'unknown_image');
        $this->dispatch('webp/test/images');
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = (string)$this->getResponse()->getBody();
        $this->assertStringContainsString('https://example.com/some/non/existing/image.png', $body);
        $this->assertStringNotContainsString('type="image/webp"', $body);
    }
}
