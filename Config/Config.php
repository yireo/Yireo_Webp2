<?php
declare(strict_types=1);

namespace Yireo\Webp2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;

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
     * @return int
     */
    public function getQualityLevel(): int
    {
        // @todo: Convert this into config value
        return 80;
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
        return true;
    }
}
