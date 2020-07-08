<?php
declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Swatches\Helper\Data;
use Yireo\Webp2\Browser\BrowserSupport;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Image\UrlReplacer;

class CorrectImagesInAjaxResponse
{
    /**
     * @var BrowserSupport
     */
    private $browserSupport;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var UrlReplacer
     */
    private $urlReplacer;

    /**
     * CorrectImagesInAjaxResponse constructor.
     *
     * @param BrowserSupport $browserSupport
     * @param Config $config
     * @param UrlReplacer $urlReplacer
     */
    public function __construct(
        BrowserSupport $browserSupport,
        Config $config,
        UrlReplacer $urlReplacer
    ) {
        $this->browserSupport = $browserSupport;
        $this->config = $config;
        $this->urlReplacer = $urlReplacer;
    }

    /**
     * @param Data $dataHelper
     * @param array $data
     * @return mixed[]
     */
    public function afterGetProductMediaGallery(Data $dataHelper, array $data): array
    {
        if (!$this->browserSupport->hasWebpSupport()) {
            return $data;
        }

        $data = $this->replaceUrls($data);
        return $data;
    }

    /**
     * @param mixed[] $dataArray
     * @return mixed[]
     */
    private function replaceUrls(array $dataArray): array
    {
        if (empty($dataArray)) {
            return $dataArray;
        }

        foreach ($dataArray as $name => $value) {
            if (is_array($value)) {
                $dataArray[$name] = $this->replaceUrls($value);
                continue;
            }

            if (!is_string($value)) {
                continue;
            }

            if (!preg_match('/\.(jpg|png)$/', $value)) {
                continue;
            }

            try {
                $dataArray[$name] = $this->urlReplacer->getWebpUrlFromImageUrl($value);
            } catch (\Exception $e) {
                continue;
            }
        }

        return $dataArray;
    }
}
