<?php
declare(strict_types=1);

namespace Yireo\Webp2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;

class Config implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('yireo_webp2/settings/enabled');
    }

    /**
     * @return int
     */
    public function getQualityLevel(): int
    {
        $qualityLevel = (int)$this->scopeConfig->getValue('yireo_webp2/settings/quality_level');
        if ($qualityLevel > 100) {
            return 100;
        }

        if ($qualityLevel < 1) {
            return 1;
        }

<<<<<<< HEAD
=======
    /**
     * @return int
     */
    public function getQualityLevel(): int
    {
        $qualityLevel = (int)$this->scopeConfig->getValue('yireo_webp2/settings/quality_level');
        if ($qualityLevel > 100) {
            return 100;
        }

        if ($qualityLevel < 1) {
            return 1;
        }

>>>>>>> a9252ddd5ae52df891012f748afd1b2dbd94c240
        return $qualityLevel;
    }

    /**
     * @return string[]
     */
    public function getConvertors(): array
    {
        return ['cwebp', 'gd', 'imagick', 'wpc', 'ewww'];
    }
}
