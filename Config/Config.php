<?php declare(strict_types=1);

namespace Yireo\Webp2\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\StoreManagerInterface;
use Yireo\Webp2\Exception\InvalidConvertorException;

class Config implements ArgumentInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    /**
     * @return bool
     */
    public function enabled(): bool
    {
        return (bool)$this->getValue('yireo_webp2/settings/enabled');
    }

    /**
     * @return int
     */
    public function getQualityLevel(): int
    {
        $qualityLevel = (int)$this->getValue('yireo_webp2/settings/quality_level');
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
        $storedConvertors = $this->getValue('yireo_webp2/settings/convertors');
        $storedConvertors = $this->stringToArray((string)$storedConvertors);
        if (empty($storedConvertors)) {
            return $allConvertors;
        }

        foreach ($storedConvertors as $storedConvertor) {
            if (!in_array($storedConvertor, $allConvertors)) {
                throw new InvalidConvertorException('Invalid convertor: "' . $storedConvertor . '"');
            }
        }

        return $storedConvertors;
    }

    /**
     * @return string
     * @throws InvalidConvertorException
     */
    public function getEncoding(): string
    {
        $allEncoding = ['lossy', 'lossless', 'auto'];
        $storedEncoding = (string)$this->getValue('yireo_webp2/settings/encoding');
        if (empty($storedEncoding)) {
            return 'lossy';
        }

        if (!in_array($storedEncoding, $allEncoding)) {
            throw new InvalidConvertorException('Invalid encoding: "' . $storedEncoding . '"');
        }

        return $storedEncoding;
    }

    /**
     * @param string $path
     * @return mixed
     */
    private function getValue(string $path)
    {
        try {
            return $this->scopeConfig->getValue(
                $path,
                ScopeInterface::SCOPE_STORE,
                $this->storeManager->getStore()
            );
        } catch (NoSuchEntityException $e) {
            return null;
        }
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
