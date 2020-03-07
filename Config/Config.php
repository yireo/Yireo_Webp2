<?php
declare(strict_types=1);

namespace Yireo\Webp2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\PageCache\Model\DepersonalizeChecker;

/**
 * Class Config
 *
 * @package Yireo\Webp2\Config
 */
class Config
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var DepersonalizeChecker
     */
    private $depersonalizeChecker;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param DepersonalizeChecker $depersonalizeChecker
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        DepersonalizeChecker $depersonalizeChecker
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->depersonalizeChecker = $depersonalizeChecker;
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_webp2/settings/enabled');
    }

    /**
     * @return bool
     */
    public function allowImageCreation(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_webp2/settings/convert_images');
    }

    /**
     * @return bool
     */
    public function addLazyLoading(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_webp2/settings/lazy_loading');
    }

    /**
     * @param LayoutInterface $block
     * @return bool
     */
    public function hasFullPageCacheEnabled(LayoutInterface $block): bool
    {
        if ($this->depersonalizeChecker->checkIfDepersonalize($block)) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getQualityLevel(): int
    {
        return (int)$this->scopeConfig->getValue('yireo_webp2/settings/quality_level');
    }

    /**
     * @return string[]
     */
    public function getConvertors(): array
    {
        return ['cwebp', 'gd', 'imagick', 'wpc', 'ewww'];
    }

    /**
     * @return bool
     */
    public function isDebugging(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_webp2/settings/debug');
    }
}
