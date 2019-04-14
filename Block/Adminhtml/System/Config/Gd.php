<?php
declare(strict_types=1);

namespace Yireo\Webp2\Block\Adminhtml\System\Config;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;

/**
 * Class Gd
 *
 * @package Yireo\Webp2\Block\Adminhtml\System\Config
 */
class Gd extends Field
{
    /**
     * Override to set a different PHTML template
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
            $this->setTemplate('config/gd.phtml');

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
     * @return bool
     */
    public function hasGdSupport(): bool
    {
        if (!function_exists('gd_info')) {
            return false;
        }

        if (!function_exists('imagecreatefromwebp')) {
            return false;
        }

        $gdInfo = gd_info();
        $webpMatch = false;
        foreach ($gdInfo as $gdInfoLine => $gdInfoSupport) {
            if (stristr($gdInfoLine, 'webp')) {
                $webpMatch = true;
                break;
            }
        }

        if ($webpMatch === false) {
            return false;
        }

        return true;
    }
}
