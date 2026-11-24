<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2\Controller\Test\Images;

class ImagesTest extends TestCase
{
    public function testIfDisabledTestPagesForwardToNoRoute()
    {
        $forward = $this->createMock(Forward::class);
        $forward->expects($this->once())->method('forward')->with('noroute')->willReturnSelf();

        $resultFactory = $this->createMock(ResultFactory::class);
        $resultFactory->method('create')->with(ResultFactory::TYPE_FORWARD)->willReturn($forward);

        $pageFactory = $this->createMock(PageFactory::class);
        $pageFactory->expects($this->never())->method('create');

        $controller = new Images(
            $this->getContext($resultFactory),
            $pageFactory,
            $this->getScopeConfig(false)
        );
        $this->assertSame($forward, $controller->execute());
    }

    public function testIfTestPagesFlagAllowsNonDeveloperMode()
    {
        $page = $this->createMock(Page::class);
        $page->expects($this->once())->method('addHandle')->with('webp_test_images_');

        $pageFactory = $this->createMock(PageFactory::class);
        $pageFactory->method('create')->willReturn($page);

        $controller = new Images(
            $this->getContext($this->createMock(ResultFactory::class)),
            $pageFactory,
            $this->getScopeConfig(true)
        );
        $this->assertSame($page, $controller->execute());
    }

    public function testIfTestPagesAddSanitizedHandle()
    {
        $page = $this->createMock(Page::class);
        $page->expects($this->once())->method('addHandle')->with('webp_test_images_multiple_images');

        $pageFactory = $this->createMock(PageFactory::class);
        $pageFactory->method('create')->willReturn($page);

        $request = $this->createMock(RequestInterface::class);
        $request->method('getParam')->with('case')->willReturn('Multiple_Images<1>');

        $context = $this->getContext($this->createMock(ResultFactory::class), $request);
        $controller = new Images(
            $context,
            $pageFactory,
            $this->getScopeConfig(true)
        );
        $this->assertSame($page, $controller->execute());
    }

    public function testIfControllerOnlyAcceptsGetRequests()
    {
        $this->assertTrue(is_subclass_of(Images::class, HttpGetActionInterface::class));
        $this->assertFalse(is_subclass_of(Images::class, HttpPostActionInterface::class));
    }

    private function getScopeConfig(bool $testPages): ScopeConfigInterface
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->with('yireo_webp2/settings/test_pages')->willReturn($testPages);
        return $scopeConfig;
    }

    private function getContext(ResultFactory $resultFactory, ?RequestInterface $request = null): Context
    {
        $context = $this->createMock(Context::class);
        $context->method('getResultFactory')->willReturn($resultFactory);
        $context->method('getRequest')->willReturn($request ?? $this->createMock(RequestInterface::class));
        return $context;
    }
}
