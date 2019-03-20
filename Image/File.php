<?php
declare(strict_types=1);

namespace Yireo\WebP2\Image;

/**
 * Class File
 *
 * @package Yireo\WebP2\Image
 */
class File
{
    /**
     * @param string $sourceFilename
     *
     * @return string
     */
    public function toWebp(string $sourceFilename): string
    {
        return preg_replace('/\.(jpg|jpeg|png)/i', '.webp', $sourceFilename);
    }
}
