<?php
declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Collection;
use Yireo\Webp2\Logger\Debugger;

/**
 * Class ReplaceGalleryImages
 *
 * @package Yireo\Webp2\Plugin
 */
class ReplaceGalleryImages
{
    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * ReplaceGalleryImages constructor.
     *
     * @param Debugger $debugger
     */
    public function __construct(
        Debugger $debugger
    ) {
        $this->debugger = $debugger;
    }

    /**
     * @param Gallery $gallery
     * @param Collection $collection
     *
     * @return array
     */
    public function afterGetMediaGalleryImages(Product $gallery, $data)
    {
        die('test');
        return [];
        //die('hurra');
        //$this->debugger->debug('GetGalleryImages', $data);
        return $data;
    }
}
