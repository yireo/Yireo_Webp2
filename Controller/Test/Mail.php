<?php
declare(strict_types=1);

namespace Yireo\Webp2\Controller\Test;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\DeploymentConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\Page;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class Mail
 *
 * @package Yireo\Webp2\Controller\Test
 */
class Mail extends Action
{
    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var LayoutFactory
     */
    private $layoutFactory;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var JsonFactory
     */
    private $jsonFactory;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DeploymentConfig
     */
    private $config;

    /**
     * Mail constructor.
     * @param TransportBuilder $transportBuilder
     * @param StoreManagerInterface $storeManager
     * @param LayoutFactory $layoutFactory
     * @param RequestInterface $request
     * @param JsonFactory $jsonFactory
     * @param ScopeConfigInterface $scopeConfig
     * @param DeploymentConfig $config
     * @param Context $context
     */
    public function __construct(
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        LayoutFactory $layoutFactory,
        RequestInterface $request,
        JsonFactory $jsonFactory,
        ScopeConfigInterface $scopeConfig,
        DeploymentConfig $config,
        Context $context
    ) {
        parent::__construct($context);
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->layoutFactory = $layoutFactory;
        $this->request = $request;
        $this->jsonFactory = $jsonFactory;
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
    }

    /**
     * @return ResultInterface
     * @throws LocalizedException
     * @throws MailException
     * @throws NoSuchEntityException
     */
    public function execute(): ResultInterface
    {
        $token = (string)$this->request->getParam('token');
        $cryptKey = (string)$this->config->get('crypt/key');
        if ($token === $cryptKey) {
            return $this->sendResult(['msg' => 'Invalid token']);
        }

        $emailTemplate = (string)$this->request->getParam('email');

        if (empty($emailTemplate)) {
            $emailTemplate = 'sales_email_invoice_template';
        }

        $receiverInfo = [
            'name' => $this->scopeConfig->getValue('trans_email_ident/general/name'),
            'email' => $this->scopeConfig->getValue('trans_email_ident/general/email'),
        ];

        $senderInfo = [
            'name' => $this->scopeConfig->getValue('trans_email_ident/general/name'),
            'email' => $this->scopeConfig->getValue('trans_email_ident/general/email'),
        ];

        $data = [
            'template' => $emailTemplate,
            'sender' => $senderInfo,
            'receiver' => $receiverInfo
        ];

        $variables = [];
        $this->transportBuilder->setTemplateIdentifier($emailTemplate)
            ->setTemplateOptions(
                [
                    'area' => Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getId(),
                ]
            )
            ->setTemplateVars($variables)
            ->setFrom($senderInfo)
            ->addTo($receiverInfo['email'], $receiverInfo['name']);

        $transport = $this->transportBuilder->getTransport();
        $transport->sendMessage();
        $data['msg'] = 'Email sent';

        return $this->sendResult($data);
    }

    /**
     * @param mixed[] $data
     * @return ResultInterface
     */
    private function sendResult(array $data): ResultInterface
    {
        $jsonResult = $this->jsonFactory->create();
        $jsonResult->setData($data);
        return $jsonResult;
    }
}
