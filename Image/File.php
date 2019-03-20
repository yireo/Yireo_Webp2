<?php
declare(strict_types=1);

namespace Yireo\WebP2\Image;

use Exception;
use Magento\Framework\Filesystem\DirectoryList;

/**
 * Class File
 *
 * @package Yireo\WebP2\Image
 */
class File
{
    /**
     * @var DirectoryList
     */
    private $directoryList;

    public function __construct(
        DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
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
        $path = $parsedUrl['path'];
        $path = preg_replace('/^\/pub\//', '/', $path);
        $path = $this->getAbsolutePathFromImagePath($path);

        return $path;
    }

    /**
     * @param string $sourceFilename
     *
     * @return string
     */
    public function toWebp(string $sourceFilename): string
    {
        return preg_replace('/\.(jpg|jpeg|png)/i', '.webp', $sourceFilename);
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
}
