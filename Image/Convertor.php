<?php
declare(strict_types=1);

namespace Yireo\WebP2\Image;

use WebPConvert\WebPConvert;
use Yireo\WebP2\Config\Config;

/**
 * Class Convertor
 *
 * @package Yireo\WebP2\Image
 */
class Convertor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Convertor constructor.
     *
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * @param string $sourceImageFilename
     * @param string $destinationImageFilename
     *
     * @return bool
     */
    public function convert(string $sourceImageFilename, string $destinationImageFilename): bool
    {
        return WebPConvert::convert($sourceImageFilename, $destinationImageFilename, $this->getOptions());
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return [
            'quality' => 'auto',
            'max-quality' => $this->config->getQualityLevel(),
            'converters' => $this->config->getConvertors(),
        ];
    }
}
