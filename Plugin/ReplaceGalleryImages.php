<?php
declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Yireo\Webp2\Image\Convertor;
use Yireo\Webp2\Image\File;
use Yireo\Webp2\Logger\Debugger;

/**
 * Class ReplaceGalleryImages
 *
 * @package Yireo\Webp2\Plugin
 */
class ReplaceGalleryImages
{
    /**
     * @var Convertor
     */
    private $convertor;

    /**
     * @var File
     */
    private $file;

    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * ReplaceGalleryImages constructor.
     *
     * @param Convertor $convertor
     * @param File $file
     * @param Debugger $debugger
     */
    public function __construct(
        Convertor $convertor,
        File $file,
        Debugger $debugger
    ) {
        $this->convertor = $convertor;
        $this->file = $file;
        $this->debugger = $debugger;
    }

    /**
     * @param Gallery $gallery
     * @param Collection|array $images
     *
     * @return array
     */
    public function afterGetGalleryImages(Gallery $gallery, $images): array
    {
        $newImages = [];

        foreach ($images as $image) {
            $newImages[] = $this->convertImage($image);
        }

        return $newImages;
    }

    /**
     * @param DataObject $image
     *
     * @return DataObject
     */
    private function convertImage(DataObject $image): DataObject
    {
        $imageTypes = ['small_image_url', 'medium_image_url', 'large_image_url'];
        foreach ($imageTypes as $imageType) {
            $imageUrl = $image->getData($imageType);
            $webpUrl = $this->file->toWebp($imageUrl);

            try {
                $this->convertor->convert($imageUrl, $webpUrl);
            } catch (\Exception $e) {
                $this->debugger->debug($e->getMessage(), [$imageUrl, $webpUrl]);
                return $image;
            }

            $image->setData($imageType, $webpUrl);
        }

        $this->debugger->debug('Image',$image->getData());
        return $image;
    }
}
