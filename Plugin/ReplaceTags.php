<?php
declare(strict_types=1);

namespace Yireo\WebP2\Plugin;

use Magento\Framework\View\LayoutInterface;
use Psr\Log\LoggerInterface;
use Yireo\WebP2\Block\Picture;
use Yireo\WebP2\Image\Convertor;
use Yireo\WebP2\Image\File;

/**
 * Class ReplaceTags
 *
 * @package Yireo\WebP2\Plugin
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ReplaceTags constructor.
     *
     * @param Convertor $convertor
     * @param File $file
     * @param LoggerInterface $logger
     */
    public function __construct(
        Convertor $convertor,
        File $file,
        LoggerInterface $logger
    ) {
        $this->convertor = $convertor;
        $this->file = $file;
        $this->logger = $logger;
    }

    /**
     * @param LayoutInterface $layout
     * @param string $output
     *
     * @return string
     */
    public function afterGetOutput(LayoutInterface $layout, string $output): string
    {
        if (preg_match_all('/<([^<]+)\ src=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>/i', $output, $matches) === false) {
            return $output;
        }

        foreach ($matches[0] as $index => $match) {
            $htmlTag = $matches[0][$index];
            $imageUrl = $matches[2][$index] . '.' . $matches[3][$index];

            $webpUrl = $this->file->toWebp($imageUrl);
            $altText = $this->getAltText($htmlTag);

            try {
                $this->convertor->convert($imageUrl, $webpUrl);
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage());
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