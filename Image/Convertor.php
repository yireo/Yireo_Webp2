<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\Filesystem\File\ReadFactory as FileReadFactory;
use Yireo\Webp2\Config\Config;

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
     * @var ConvertWrapper
     */
    private $convertWrapper;
    /**
     * @var FileReadFactory
     */
    private $fileReadFactory;

    /**
     * Convertor constructor.
     *
     * @param Config $config
     * @param File $file
     * @param ConvertWrapper $convertWrapper
     * @param FileReadFactory $fileReadFactory
     */
    public function __construct(
        Config $config,
        File $file,
        ConvertWrapper $convertWrapper,
        FileReadFactory $fileReadFactory
    ) {
        $this->config = $config;
        $this->file = $file;
        $this->convertWrapper = $convertWrapper;
        $this->fileReadFactory = $fileReadFactory;
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

        $this->convertWrapper->convert($sourceImageFilename, $destinationImageFilename);
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
        if (!$this->fileExists($sourceImageFilename)) {
            return false;
        }

        if (!$this->fileExists($destinationImageFilename)) {
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
        if ($this->fileExists($filePath)) {
            return true;
        }

        return false;
    }

    /**
     * @param $filePath
     * @return bool
     */
    private function fileExists($filePath): bool
    {
        try {
            $this->fileReadFactory->create($filePath, 'file');
            return true;
        } catch (FileSystemException $fileSystemException) {
            return false;
        }
    }
}
