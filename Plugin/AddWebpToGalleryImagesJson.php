<?php declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\Serialize\SerializerInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Logger\Debugger;
use Yireo\Webp2\Convertor\Convertor;

class AddWebpToGalleryImagesJson
{
    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var Convertor
     */
    private $convertor;
    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * AddImagesToConfigurableJsonConfig constructor.
     * @param SerializerInterface $serializer
     * @param Convertor $convertor
     * @param Debugger $debugger
     */
    public function __construct(
        SerializerInterface $serializer,
        Convertor $convertor,
        Debugger $debugger
    ) {
        $this->serializer = $serializer;
        $this->convertor = $convertor;
        $this->debugger = $debugger;
    }

    /**
     * @param Gallery $subject
     * @param string $galleryImagesJson
     * @return string
     */
    public function afterGetGalleryImagesJson(Gallery $subject, string $galleryImagesJson): string
    {
        $jsonData = $this->serializer->unserialize($galleryImagesJson);
        $jsonData = $this->appendImages($jsonData);
        return $this->serializer->serialize($jsonData);
    }

    /**
     * @param array $images
     * @return array
     */
    private function appendImages(array $images): array
    {
        foreach ($images as $id => $imageData) {
            foreach (['thumb', 'img', 'full'] as $imageType) {
                if (empty($imageData[$imageType])) {
                    continue;
                }
                $imageData["{$imageType}_webp"] = $this->getWebpUrl($imageData[$imageType]);
            }
            $images[$id] = $imageData;
        }

        return $images;
    }

    /**
     * @param string $url
     * @return string
     */
    private function getWebpUrl(string $url): string
    {
        try {
            return $this->convertor->getImage($url)->getUrl();
        } catch (ConvertorException $e) {
            $this->debugger->debug($e->getMessage(), ['url' => $url]);
            return $url;
        }
    }
}
