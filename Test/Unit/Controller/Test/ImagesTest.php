<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Controller\Test;

use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;
use Magento\Framework\Controller\Result\Forward;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2\Controller\Test\Images;

class ImagesTest extends TestCase
{
    /**
     * @dataProvider getNonDeveloperModes
     */
    public function testIfNonDeveloperModeForwardsToNoRoute(string $mode)
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
            $this->getState($mode),
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
            $this->getState(State::MODE_PRODUCTION),
            $this->getScopeConfig(true)
        );
        $this->assertSame($page, $controller->execute());
    }

    public function testIfDeveloperModeAddsSanitizedHandle()
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
            $this->getState(State::MODE_DEVELOPER),
            $this->getScopeConfig(false)
        );
        $this->assertSame($page, $controller->execute());
    }

    public static function getNonDeveloperModes(): array
    {
        return [
            [State::MODE_PRODUCTION],
            [State::MODE_DEFAULT],
        ];
    }

    private function getScopeConfig(bool $testPages): ScopeConfigInterface
    {
        $scopeConfig = $this->createMock(ScopeConfigInterface::class);
        $scopeConfig->method('isSetFlag')->with('yireo_webp2/settings/test_pages')->willReturn($testPages);
        return $scopeConfig;
    }

    private function getState(string $mode): State
    {
        $state = $this->createMock(State::class);
        $state->method('getMode')->willReturn($mode);
        return $state;
    }

    private function getContext(ResultFactory $resultFactory, ?RequestInterface $request = null): Context
    {
        $context = $this->createMock(Context::class);
        $context->method('getResultFactory')->willReturn($resultFactory);
        $context->method('getRequest')->willReturn($request ?? $this->createMock(RequestInterface::class));
        return $context;
    }
}
