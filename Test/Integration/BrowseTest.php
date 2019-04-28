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
     * @magentoAdminConfigFixture system/yireo_webp/enabled 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        $this->dispatch('/');
        $body = $this->getResponse()->getBody();
        $this->assertContains('type="image/webp"', $body);
    }

    /**
     * @magentoAdminConfigFixture system/yireo_webp/enabled 0
     */
    public function testIfHtmlContainsNoWebpImages()
    {
        $this->dispatch('/');
        $body = $this->getResponse()->getBody();
        $this->assertNotContains('type="image/webp"', $body);
    }
}
