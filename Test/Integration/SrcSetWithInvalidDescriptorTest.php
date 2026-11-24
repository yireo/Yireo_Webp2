<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

class SrcSetWithInvalidDescriptorTest extends Common
{
    /**
     * @magentoAdminConfigFixture yireo_nextgenimages/settings/enabled 1
     * @magentoAdminConfigFixture yireo_nextgenimages/settings/convert_images 1
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     * @magentoConfigFixture current_store yireo_webp2/settings/test_pages 1
     */
    public function testIfInvalidSrcSetDescriptorDoesNotBreakOutOfAttribute(): void
    {
        $this->fixtureImageFiles();

        $this->getRequest()->setParam('case', 'srcset_with_invalid_descriptor');
        $this->dispatch('webp/test/images');
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());

        $body = (string)$this->getResponse()->getBody();
        $this->assertStringContainsString('type="image/webp"', $body);
        $this->assertDoesNotMatchRegularExpression('/<source[^>]*\sonerror=/i', $body);
        $this->assertStringNotContainsString('"onerror="', $body);
    }
}
