<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception as ExceptionAlias;
use Magento\Framework\View\LayoutInterface;
use Yireo\Webp2\Block\Picture;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Logger\Debugger;

class HtmlReplacer
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
     * @var Config
     */
    private $config;

    /**
     * @var UrlReplacer
     */
    private $urlReplacer;

    /**
     * ReplaceTags constructor.
     *
     * @param Convertor $convertor
     * @param File $file
     * @param Debugger $debugger
     * @param Config $config
     * @param UrlReplacer $urlReplacer
     */
    public function __construct(
        Convertor $convertor,
        File $file,
        Debugger $debugger,
        Config $config,
        UrlReplacer $urlReplacer
    ) {
        $this->convertor = $convertor;
        $this->file = $file;
        $this->debugger = $debugger;
        $this->config = $config;
        $this->urlReplacer = $urlReplacer;
    }

    /**
     * @param LayoutInterface $layout
     * @param string $html
     * @return string
     * @throws ExceptionAlias
     */
    public function replaceImagesInHtml(LayoutInterface $layout, string $html): string
    {
        $regex = '/<([^<]+)\ (data\-src|src)=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>(\s*)(<(\/[a-z]+))?/msi';
        if (preg_match_all($regex, $html, $matches) === false) {
            return $html;
        }

        foreach ($matches[0] as $index => $match) {
            $nextTag = $matches[8][$index];
            $fullSearchMatch = $matches[0][$index];
            $imageUrl = $matches[3][$index] . '.' . $matches[4][$index];

            if (!$this->isAllowedByNextTag($nextTag)) {
                continue;
            }

            if (!$this->isAllowedByImageUrl($imageUrl)) {
                continue;
            }

            $webpUrl = $this->urlReplacer->getWebpUrlFromImageUrl($imageUrl);
            if (!$webpUrl) {
                continue;
            }

            $htmlTag = preg_replace('/>(.*)/msi', '>', $fullSearchMatch);
            $newHtmlTag = $this->getNewHtmlTag($layout, $imageUrl, $webpUrl, $htmlTag, $matches[2][$index] === 'data-src');
            $replacement = $nextTag ? $newHtmlTag . '<' . $nextTag : $newHtmlTag;
            $html = str_replace($fullSearchMatch, $replacement, $html);
        }

        return $html;
    }

    /**
     * @param string $nextTag
     * @return bool
     */
    private function isAllowedByNextTag(string $nextTag): bool
    {
        if ($nextTag === '/picture') {
            return false;
        }

        return true;
    }

    /**
     * @param string $imageUrl
     * @return bool
     */
    private function isAllowedByImageUrl(string $imageUrl): bool
    {
        if (strpos($imageUrl, '/media/captcha/') !== false) {
            return false;
        }

        return true;
    }

    /**
     * @param LayoutInterface $layout
     * @param string $imageUrl
     * @param string $webpUrl
     * @param $htmlTag
     * @return string
     */
    private function getNewHtmlTag(LayoutInterface $layout, string $imageUrl, string $webpUrl, $htmlTag, bool $isDataSrc = false): string
    {
        return (string)$this->getPictureBlock($layout)
            ->setOriginalImage($imageUrl)
            ->setWebpImage($webpUrl)
            ->setAltText($this->getAttributeText($htmlTag, 'alt'))
            ->setOriginalTag($htmlTag)
            ->setClass($this->getAttributeText($htmlTag, 'class'))
            ->setWidth($this->getAttributeText($htmlTag, 'width'))
            ->setHeight($this->getAttributeText($htmlTag, 'height'))
            ->setIsDataSrc($isDataSrc)
            ->toHtml();
    }

    /**
     * @param string $htmlTag
     * @param string $attribute
     * @return string
     */
    private function getAttributeText(string $htmlTag, string $attribute): string
    {
        if (preg_match('/\ ' . $attribute . '=\"([^\"]+)/', $htmlTag, $match)) {
            $altText = $match[1];
            $altText = strtr($altText, ['"' => '', "'" => '']);
            return $altText;
        }

        return '';
    }

    /**
     * Get Picture Block-class from the layout
     *
     * @param LayoutInterface $layout
     * @return Picture
     */
    private function getPictureBlock(LayoutInterface $layout): Picture
    {
        /** @var Picture $block */
        $block = $layout->createBlock(Picture::class);
        $block->setDebug($this->config->isDebugging());
        return $block;
    }
}
