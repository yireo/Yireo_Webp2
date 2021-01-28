<?php declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\Webp2\Convertor\Convertor;

class AddWebpToConfigurableJsonConfig
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
     * @param Configurable $subject
     * @param string $jsonConfig
     * @return string
     * @throws ConvertorException
     */
    public function afterGetJsonConfig(Configurable $subject, string $jsonConfig): string
    {
        $jsonData = $this->serializer->unserialize($jsonConfig);

        if (isset($jsonData['images'])) {
            $jsonData['images'] = $this->appendImages($jsonData['images']);
        }

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
        foreach ($images as $id => $imagesData) {
            foreach ($imagesData as $imageDataIndex => $imageData) {
                $imageData['thumb_webp'] = $this->getWebpUrl($imageData['thumb']);
                $imageData['img_webp'] = $this->getWebpUrl($imageData['img']);
                $imageData['full_webp'] = $this->getWebpUrl($imageData['full']);
                $imagesData[$imageDataIndex] = $imageData;
            }

            $images[$id] = $imagesData;
        }

        return $images;
    }

    /**
     * @param string $url
     * @return string
     * @throws ConvertorException
     */
    private function getWebpUrl(string $url): string
    {
        return $this->convertor->getSourceImage($url)->getUrl();
    }
}