<?php
declare(strict_types=1);

namespace Yireo\Webp2\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\State;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\View\Result\PageFactory;
use Magento\Store\Model\ScopeInterface;

class Images extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * @var State
     */
    private $appState;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @param Context     $context
     * @param PageFactory $pageFactory
     * @param State       $appState
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory,
        State $appState,
        ScopeConfigInterface $scopeConfig
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
        $this->appState = $appState;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return ResultInterface
     */
    public function execute()
    {
        if (!$this->isAllowed()) {
            /** @var \Magento\Framework\Controller\Result\Forward $forward */
            $forward = $this->resultFactory->create(ResultFactory::TYPE_FORWARD);
            return $forward->forward('noroute');
        }

        $page = $this->pageFactory->create();

        $case = strtolower((string)$this->_request->getParam('case'));
        $case = preg_replace('/([^a-z\_]+)/', '', $case);
        $handle = 'webp_test_images_' . $case;
        $page->addHandle($handle);

        return $page;
    }

    private function isAllowed(): bool
    {
        if ($this->appState->getMode() === State::MODE_DEVELOPER) {
            return true;
        }

        return $this->scopeConfig->isSetFlag('yireo_webp2/settings/test_pages', ScopeInterface::SCOPE_STORE);
    }
}
