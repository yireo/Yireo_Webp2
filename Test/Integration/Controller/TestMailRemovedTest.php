<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;

class TestMailRemovedTest extends AbstractController
{
    /**
     * @magentoAppArea frontend
     */
    public function testIfMailControllerIsNotAvailable()
    {
        $this->getRequest()->setParam('token', 'foobar');
        $this->dispatch('webp/test/mail');
        $this->assert404NotFound();
    }
}
