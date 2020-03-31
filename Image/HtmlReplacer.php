<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception as ExceptionAlias;
use Magento\Framework\View\LayoutInterface;
use Yireo\Webp2\Block\Picture;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Logger\Debugger;

/**
 * Class HtmlReplacer
 *
 * @package Yireo\Webp2\Image
 */
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
     * ReplaceTags constructor.
     *
     * @param Convertor $convertor
     * @param File $file
     * @param Debugger $debugger
     * @param Config $config
     */
    public function __construct(
        Convertor $convertor,
        File $file,
        Debugger $debugger,
        Config $config
    ) {
        $this->convertor = $convertor;
        $this->file = $file;
        $this->debugger = $debugger;
        $this->config = $config;
    }

    /**
     * @param LayoutInterface $layout
     * @param string $html
     * @return string
     * @throws ExceptionAlias
     */
    public function replaceImagesInHtml(LayoutInterface $layout, string $html): string
    {
        $regex = '/<([^<]+)\ src=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>(\s*)<(\/?)([a-z]+)/msi';
        if (preg_match_all($regex, $html, $matches) === false) {
            return $html;
        }

        foreach ($matches[0] as $index => $match) {
            $nextTag = $matches[6][$index] . $matches[7][$index];
            $fullSearchMatch = $matches[0][$index];
            $imageUrl = $matches[2][$index] . '.' . $matches[3][$index];

            if ($nextTag === '/picture') {
                continue;
            }

            if (strpos($imageUrl, '/media/captcha/') !== false) {
                continue;
            }

            $webpUrl = $this->getWebpUrlFromImageUrl($imageUrl);
            if (!$webpUrl) {
                continue;
            }

            $htmlTag = preg_replace('/>(.*)/msi', '>', $fullSearchMatch);
            $newHtmlTag = $this->getNewHtmlTag($layout, $imageUrl, $webpUrl, $htmlTag);
            $replacement = $newHtmlTag . '<' . $nextTag;
            $html = str_replace($fullSearchMatch, $replacement, $html);
        }

        return $html;
    }

    /**
     * @param string $imageUrl
     * @return string
     * @throws ExceptionAlias
     */
    private function getWebpUrlFromImageUrl(string $imageUrl): string
    {
        $webpUrl = $this->file->toWebp($imageUrl);

        try {
            $result = $this->convertor->convert($imageUrl, $webpUrl);
        } catch (ExceptionAlias $e) {
            if ($this->config->isDebugging()) {
                throw $e;
            }

            $result = false;
            $this->debugger->debug($e->getMessage(), [$imageUrl, $webpUrl]);
        }

        if (!$result && !$this->convertor->urlExists($webpUrl)) {
            return '';
        }

        return $webpUrl;
    }

    /**
     * @param LayoutInterface $layout
     * @param string $imageUrl
     * @param string $webpUrl
     * @param $htmlTag
     * @return string
     */
    private function getNewHtmlTag(LayoutInterface $layout, string $imageUrl, string $webpUrl, $htmlTag): string
    {
        return (string)$this->getPictureBlock($layout)
            ->setOriginalImage($imageUrl)
            ->setWebpImage($webpUrl)
            ->setAltText($this->getAttributeText($htmlTag, 'alt'))
            ->setOriginalTag($htmlTag)
            ->setClass($this->getAttributeText($htmlTag, 'class'))
            ->setWidth($this->getAttributeText($htmlTag, 'width'))
            ->setHeight($this->getAttributeText($htmlTag, 'height'))
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
