<?php
namespace Yireo\Webp2\Plugin;

use Magento\Framework\App\ResponseInterface;
use Yireo\Webp2\Browser\BrowserSupport;

class PageAddBodyClass {

    /**
     * \Magento\Framework\View\Element\Context context
     *
     * @var array
     */
    private $context;
    /**
     * BrowserSupport browserSupport
     *
     * @var array
     */
    private $browserSupport;
    /**
     * Registry registry
     *
     * @var array
     */
    private $registry;
    /**
     * PageAddBodyClass constructor.
     *
     * @param Context $context
     * @param BrowserSupport $browserSupport
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        BrowserSupport $browserSupport,
        \Magento\Framework\Registry $registry
    ) {
        $this->context = $context;
        $this->browserSupport = $browserSupport;
        $this->registry = $registry;
    }
    
    /**
     * Interceptor of renderPage()
     *
     * @param Page $subject
     * @param ResponseInterface $response
     * @return string    
     */
    public function beforeRenderResult(\Magento\Framework\View\Result\Page $subject, ResponseInterface $response) {		
        $webp_enabled = $this->registry->registry('webp_enabled');		
        if (!$webp_enabled) {
            if ($this->browserSupport->hasWebpSupport() || $this->browserSupport->acceptsWebpHeader() || $this->browserSupport->isChromeBrowser() || $this->browserSupport->hasCookie()) {
                $this->addBodyclass($subject);				
                $this->registry->register('webp_enabled', 1);
            } else {
                $this->registry->register('webp_enabled', 2);
            }
        } else if ($webp_enabled === 1) {
            $this->addBodyclass($subject);			
        }
        return [$response];
    }
    /**
     * @param Page $subject       
     */
    private function addBodyclass($subject) {
        $subject->getConfig()->addBodyClass('webp');
    }
}
