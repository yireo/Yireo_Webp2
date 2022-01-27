<?php declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\Serialize\SerializerInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Logger\Debugger;
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
     * @param Configurable $subject
     * @param string $jsonConfig
     * @return string
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
