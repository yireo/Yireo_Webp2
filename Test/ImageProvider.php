<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class ImageProvider
 * @package Yireo\Webp2\Test
 */
class ImageProvider implements ArgumentInterface
{
    /**
     * @return array
     */
    public function getImages(): array
    {
        return [
            'Yireo_Webp2/images/test/flowers.jpg',
            'Yireo_Webp2/images/test/goku.jpg',
            'Yireo_Webp2/images/test/transparent-dragon.png',
        ];
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        $images = $this->getImages();
        return $images[0];
    }

    /**
     * @return string
     */
    public function getNonExistingImage(): string
    {
        return 'Yireo_Webp2/images/test/non-existing.jpg';
    }
}
