<?php
declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Magento\Framework\View\LayoutInterface;
use Yireo\Webp2\Block\Picture;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Image\Convertor;
use Yireo\Webp2\Image\File;
use Yireo\Webp2\Logger\Debugger;

/**
 * Class ReplaceTags
 *
 * @package Yireo\Webp2\Plugin
 */
class ReplaceTags
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
     * @param string $output
     *
     * @return string
     */
    public function afterGetOutput(LayoutInterface $layout, string $output): string
    {
        if ($this->config->enabled() === false) {
            return $output;
        }

        if (preg_match_all('/<([^<]+)\ src=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>/mi', $output, $matches) === false) {
            return $output;
        }

        foreach ($matches[0] as $index => $match) {
            $htmlTag = $matches[0][$index];
            $imageUrl = $matches[2][$index] . '.' . $matches[3][$index];

            $webpUrl = $this->file->toWebp($imageUrl);
            $altText = $this->getAltText($htmlTag);

            try {
                $result = $this->convertor->convert($imageUrl, $webpUrl);
            } catch (\Exception $e) {
                $this->debugger->debug($e->getMessage(), [$imageUrl, $webpUrl]);
            }

            if (!$result && !$this->convertor->urlExists($webpUrl)) {
                continue;
            }

            $newHtmlTag = $this->getPictureBlock($layout)
                ->setOriginalImage($imageUrl)
                ->setWebpImage($webpUrl)
                ->setAltText($altText)
                ->setOriginalTag($htmlTag)
                ->toHtml();

            $output = str_replace($htmlTag, $newHtmlTag, $output);
        }

        return $output;
    }

    /**
     * @param string $htmlTag
     *
     * @return string
     */
    private function getAltText(string $htmlTag): string
    {
        if (preg_match('/\ alt=\"([^\"]+)/', $htmlTag, $match)) {
            $altText = $match[1];
            $altText = strtr($altText, ['"' => '', "'" => '']);
            return $altText;
        }

        return '';
    }

    /**
     * @param LayoutInterface $layout
     *
     * @return Picture
     */
    private function getPictureBlock(LayoutInterface $layout): Picture
    {
        return $layout->createBlock(Picture::class);
    }
}
