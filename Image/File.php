<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception;
use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class File
 *
 * @package Yireo\Webp2\Image
 */
class File
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var ReadFactory
     */
    private $readFactory;

    /**
     * File constructor.
     *
     * @param DirectoryList $directoryList
     * @param ReadFactory $readFactory
     */
    public function __construct(
        DirectoryList $directoryList,
        ReadFactory $readFactory
    ) {
        $this->directoryList = $directoryList;
        $this->readFactory = $readFactory;
    }

    /**
     * @param string $url
     *
     * @return string
     * @throws Exception
     */
    public function resolve(string $url): string
    {
        $parsedUrl = parse_url($url);
        if (!$parsedUrl) {
            return '';
        }

        $path = $parsedUrl['path'];
        $path = preg_replace('/^\/pub\//', '/', (string)$path);
        $path = preg_replace('/\/static\/version([0-9]+\/)/', '/static/', (string)$path);
        $path = $this->getAbsolutePathFromImagePath((string)$path);

        return $path;
    }

    /**
     * @param string $sourceFilename
     *
     * @return string
     */
    public function toWebp(string $sourceFilename): string
    {
        return (string) preg_replace('/\.(jpg|jpeg|png)/i', '.webp', $sourceFilename);
    }

    /**
     * @param string $imagePath
     *
     * @return string
     */
    public function getAbsolutePathFromImagePath(string $imagePath) : string
    {
        return $this->directoryList->getRoot() . '/pub' . $imagePath;
    }

    /**
     * @param string $filePath
     *
     * @return int
     */
    public function getModificationTime(string $filePath): int
    {
        $read = $this->readFactory->create($filePath);
        // @todo: This call always leads to false for some reason?
        //if (!$read->isExist($filePath)) {
        //    return 0;
        //}

        if (!file_exists($filePath)) {
            return 0;
        }

        return (int) filemtime($filePath);
    }

    /**
     * @param string $targetFile
     * @param string $comparisonFile
     *
     * @return bool
     */
    public function isNewerThan(string $targetFile, string $comparisonFile): bool
    {
        $targetFileModificationTime = $this->getModificationTime($targetFile);
        if ($targetFileModificationTime === 0) {
            return false;
        }

        $comparisonFileModificationTime = $this->getModificationTime($comparisonFile);
        if ($comparisonFileModificationTime === 0) {
            return true;
        }

        if ($targetFileModificationTime > $comparisonFileModificationTime) {
            return true;
        }

        return false;
    }
}
