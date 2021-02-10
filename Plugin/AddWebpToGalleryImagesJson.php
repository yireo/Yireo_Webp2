<?php declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Catalog\Block\Product\View\Gallery;
use Magento\Framework\Serialize\SerializerInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
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
     * AddImagesToConfigurableJsonConfig constructor.
     * @param SerializerInterface $serializer
     * @param Convertor $convertor
     */
    public function __construct(
        SerializerInterface $serializer,
        Convertor $convertor
    ) {
        $this->serializer = $serializer;
        $this->convertor = $convertor;
    }

    /**
     * @param Gallery $subject
     * @param string $galleryImagesJson
     * @return string
     * @throws ConvertorException
     */
    public function afterGetGalleryImagesJson(Gallery $subject, string $galleryImagesJson): string
    {
        $jsonData = $this->serializer->unserialize($galleryImagesJson);
        $jsonData = $this->appendImages($jsonData);
        $jsonConfig = $this->serializer->serialize($jsonData);
        return $jsonConfig;
    }

    /**
     * @param array $images
     * @return array
     * @throws ConvertorException
     */
    private function appendImages(array $images): array
    {
        foreach ($images as $id => $imageData) {
            $imageData['thumb_webp'] = $this->getWebpUrl($imageData['thumb']);
            $imageData['img_webp'] = $this->getWebpUrl($imageData['img']);
            $imageData['full_webp'] = $this->getWebpUrl($imageData['full']);
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
            return $this->convertor->getSourceImage($url)->getUrl();
        } catch(ConvertorException $ex) {
            return $url;
        }
    }
}
