<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\WebPConvert;
use Yireo\Webp2\Config\Config;

/**
 * Class ConvertWrapper to wrap third party wrapper for purpose of preference overrides and testing
 * @package Yireo\Webp2\Image
 */
class ConvertWrapper
{
    /**
     * @var Config
     */
    private $config;

    /**
     * ConvertWrapper constructor.
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
     * @throws ConversionFailedException
     */
    public function convert(string $sourceImageFilename, string $destinationImageFilename): void
    {
        WebPConvert::convert($sourceImageFilename, $destinationImageFilename, $this->getOptions());
    }

    /**
     * @return mixed[]
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
