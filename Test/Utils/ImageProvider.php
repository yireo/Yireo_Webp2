<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Utils;

use Magento\Framework\View\Element\Block\ArgumentInterface;

class ImageProvider implements ArgumentInterface
{
    /**
     * @return string[]
     */
    public function getImages(): array
    {
        return [
            'images/test/flowers.jpg',
            'images/test/goku.jpg',
            'images/test/transparent-dragon.png',
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
        return 'images/test/non-existing.jpg';
    }
}
