<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Mtf\Config\FileResolver\ScopeConfig;
use Magento\TestFramework\TestCase\AbstractController;

/**
 * Class BrowseTest
 * @package Yireo\Webp2\Test\Integration
 */
class BrowseTest extends AbstractController
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_with_image.php
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        /** @var ScopeConfigInterface $scopeConfig */
        $scopeConfig = $this->_objectManager->get(ScopeConfigInterface::class);
        $this->assertTrue((bool)$scopeConfig->getValue('yireo_webp2/settings/enabled'));

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
     * @magentoAdminConfigFixture yireo_webp2/settings/enabled 0
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
        if (preg_match('/<img\ (.*)src="(.*)\.(jpg|png)"([^>]+)>/msi', $body)) {
            return true;
        }

        return false;
    }
}
