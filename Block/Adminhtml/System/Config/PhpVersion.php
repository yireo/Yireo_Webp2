<?php
declare(strict_types=1);

namespace Yireo\Webp2\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

class PhpVersion extends Field
{
    /**
     * Override to set a different PHTML template
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('config/php_version.phtml');

        return $this;
    }

    /**
     * Override to render the template instead of the regular output
     *
     * @param AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(AbstractElement $element)
    {
        return $this->toHtml();
    }

    /**
     * Check if GD supports WebP
     *
     * @return string
     */
    public function getPhpVersion(): string
    {
        return (string)phpversion();
    }
}
