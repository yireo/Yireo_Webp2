<?php declare(strict_types=1);

namespace Yireo\Webp2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Yireo\Webp2\Exception\InvalidConvertorException;

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

        return $qualityLevel;
    }

    /**
     * @return string[]
     * @throws InvalidConvertorException
     */
    public function getConvertors(): array
    {
        $allConvertors = ['cwebp', 'gd', 'imagick', 'wpc', 'ewww'];
        $storedConvertors = $this->scopeConfig->getValue('yireo_webp2/settings/convertors');
        $storedConvertors = $this->stringToArray((string)$storedConvertors);
        if (empty($storedConvertors)) {
            return $allConvertors;
        }

        foreach ($storedConvertors as $storedConvertor) {
            if (!in_array($storedConvertor, $allConvertors)) {
                throw new InvalidConvertorException('Invalid convertor: "'.$storedConvertor.'"');
            }
        }

        return $storedConvertors;
    }

    /**
     * @param string $string
     * @return array
     */
    private function stringToArray(string $string): array
    {
        $array = [];
        $strings = explode(',', $string);
        foreach ($strings as $string) {
            $string = trim($string);
            if ($string) {
                $array[] = $string;
            }
        }

        return $array;
    }
}
