<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception;
use Magento\Framework\View\Asset\File\NotFoundException;
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
     * @return bool
     * @throws NotFoundException
     * @throws Exception
     */
    public function convert(string $sourceImageUrl, string $destinationImageUrl): bool
    {
        $sourceImageFilename = $this->getPathFromUrl($sourceImageUrl);
        $destinationImageFilename = $this->getPathFromUrl($destinationImageUrl);

        if (!$this->needsConversion($sourceImageFilename, $destinationImageFilename)) {
            return false;
        }

        if ($this->config->allowImageCreation() === false) {
            return false;
        }

        WebPConvert::convert($sourceImageFilename, $destinationImageFilename, $this->getOptions());
        return true;
    }

    /**
     * @param string $sourceImageFilename
     * @param string $destinationImageFilename
     * @return bool
     * @throws NotFoundException
     */
    private function needsConversion(string $sourceImageFilename, string $destinationImageFilename): bool
    {
        if (!file_exists($sourceImageFilename)) {
            throw new NotFoundException($sourceImageFilename . ' is not found');
        }

        if (!file_exists($destinationImageFilename)) {
            return true;
        }

        if ($this->file->isNewerThan($destinationImageFilename, $sourceImageFilename)) {
            return false;
        }

        return false;
    }

    /**
     * @param string $url
     * @return string
     * @throws Exception
     */
    private function getPathFromUrl(string $url): string
    {
        return $this->file->resolve($url);
    }

    /**
     * @param string $url
     * @return bool
     * @throws Exception
     */
    public function urlExists(string $url): bool
    {
        $filePath = $this->file->resolve($url);
        if (file_exists($filePath)) {
            return true;
        }

        return false;
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
