<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Controller;

use Magento\TestFramework\TestCase\AbstractController;

class TestMailTest extends AbstractController
{
    /**
     * @magentoAppArea frontend
     */
    public function testIfMissingTokenIsRejected()
    {
        $this->dispatch('webp/test/mail');
        $this->assertSame(['msg' => 'Invalid token'], $this->getJsonBody());
    }

    /**
     * @magentoAppArea frontend
     */
    public function testIfInvalidTokenIsRejected()
    {
        $this->getRequest()->setParam('token', 'foobar');
        $this->dispatch('webp/test/mail');
        $this->assertSame(['msg' => 'Invalid token'], $this->getJsonBody());
    }

    private function getJsonBody(): array
    {
        $this->assertSame(200, $this->getResponse()->getHttpResponseCode());
        return (array)json_decode((string)$this->getResponse()->getBody(), true);
    }
}
