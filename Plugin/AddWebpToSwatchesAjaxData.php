<?php declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Swatches\Helper\Data as SwatchesDataHelper;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Logger\Debugger;
use Yireo\Webp2\Convertor\Convertor;

class AddWebpToSwatchesAjaxData
{
    /**
     * @var Convertor
     */
    private $convertor;

    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * AddWebpToSwatchesAjaxData constructor.
     * @param Convertor $convertor
     * @param Debugger $debugger
     */
    public function __construct(
        Convertor $convertor,
        Debugger $debugger
    ) {
        $this->convertor = $convertor;
        $this->debugger = $debugger;
    }

    /**
     * @param SwatchesDataHelper $subject
     * @param array $images
     * @return array
     */
    public function afterGetProductMediaGallery(SwatchesDataHelper $subject, array $images): array
    {
        $types = ['large', 'medium', 'small'];
        foreach ($types as $type) {
            if (!isset($images[$type])) {
                continue;
            }

            $images[$type . '_webp'] = $this->getWebpUrl($images[$type]);
        }

        if (!isset($images['gallery'])) {
            return $images;
        }

        foreach ($images['gallery'] as $galleryIndex => $galleryImages) {
            foreach ($types as $type) {
                if (!isset($images[$type])) {
                    continue;
                }

                $images['gallery'][$galleryIndex][$type . '_webp'] = $this->getWebpUrl($images[$type]);
            }
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
