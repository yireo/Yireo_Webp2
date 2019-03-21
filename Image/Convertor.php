<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception;
use WebPConvert\WebPConvert;
use Yireo\Webp2\Config\Config;

/**
 * Class Convertor
 *
 * @package Yireo\Webp2\Image
 */
class Convertor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var File
     */
    private $file;

    /**
     * Convertor constructor.
     *
     * @param Config $config
     * @param File $file
     */
    public function __construct(
        Config $config,
        File $file
    ) {
        $this->config = $config;
        $this->file = $file;
    }

    /**
     * @param string $sourceImageUrl
     * @param string $destinationImageUrl
     *
     * @return bool
     * @throws Exception
     */
    public function convert(string $sourceImageUrl, string $destinationImageUrl): bool
    {
        $sourceImageFilename = $this->getPathFromUrl($sourceImageUrl);
        $destinationImageFilename = $this->getPathFromUrl($destinationImageUrl);

        if(!$this->file->isNewerThan($sourceImageFilename, $destinationImageFilename)) {
            return false;
        }

        return WebPConvert::convert($sourceImageFilename, $destinationImageFilename, $this->getOptions());
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws Exception
     */
    private function getPathFromUrl(string $url): string
    {
        return $this->file->resolve($url);
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
