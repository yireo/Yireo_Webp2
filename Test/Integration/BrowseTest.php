<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\TestFramework\TestCase\AbstractController;

/**
 * Class BrowseTest
 * @package Yireo\Webp2\Test\Integration
 */
class BrowseTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_with_image.php
     * @magentoAdminConfigFixture system/yireo_webp/enabled 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        $this->dispatch('/catalog/product/view/id/1');
        $body = $this->getResponse()->getBody();

        if ($this->checkBodyForValidImgTags($body)) {
            $this->assertContains('type="image/webp"', $body);
            return;
        }

        $this->markTestSkipped('No replaceable <img> tags found in body');
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_with_image.php
     * @magentoAdminConfigFixture system/yireo_webp/enabled 0
     */
    public function testIfHtmlContainsNoWebpImages()
    {
        $this->dispatch('/catalog/product/view/id/1');
        $body = $this->getResponse()->getBody();

        if ($this->checkBodyForValidImgTags($body)) {
            $this->assertNotContains('type="image/webp"', $body);
            return;
        }

        $this->markTestSkipped('No replaceable <img> tags found in body: '.$body);
    }

    /**
     * @param string $body
     * @return bool
     */
    private function checkBodyForValidImgTags(string $body): bool
    {
        $body = str_replace("\n", "", $body);
        if (preg_match('/<img\ (.*)src="(.*)\.(jpg|png)"([^>]+)>/', $body)) {
            return true;
        }

        return false;
    }
}
