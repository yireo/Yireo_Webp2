<?php
declare(strict_types=1);

namespace Yireo\Webp2\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\Page;
use Magento\Framework\View\Result\PageFactory;

/**
 * Class Images
 * @package Mageplaza\HelloWorld\Controller\Index
 */
class Images extends Action
{
    /**
     * @var PageFactory
     */
    protected $pageFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param PageFactory $pageFactory
     */
    public function __construct(
        Context $context,
        PageFactory $pageFactory
    ) {
        parent::__construct($context);
        $this->pageFactory = $pageFactory;
    }

    /**
     * @return Page
     */
    public function execute()
    {
        $page = $this->pageFactory->create();

        $case = (string) $this->_request->getParam('case');

        if ($case == 'multiple') {
            $page->addHandle('webp_test_images_multiple');
        }

        if ($case == 'multiple_same') {
            $page->addHandle('webp_test_images_multiple_same');
        }

        return $page;
    }
}
