<?php
declare(strict_types=1);

namespace Yireo\Webp2\Block;

use Magento\Framework\View\Element\Template;

/**
 * Class Picture
 *
 * @package Yireo\Webp2\Block
 */
class Picture extends Template
{
    /**
     * @var string
     */
    protected $_template = 'picture.phtml';

    /**
     * @var string
     */
    private $webpImage = '';

    /**
     * @var string
     */
    private $originalImage = '';

    /**
     * @var string
     */
    private $altText = '';

    /**
     * @var string
     */
    private $width = '';

    /**
     * @var string
     */
    private $height = '';

    /**
     * @var string
     */
    private $originalTag = '';

    /**
     * @var bool
     */
    private $debug = false;

    /**
     * @var string
     */
    private $class = '';

    /**
     * @return string
     */
    public function getWebpImage(): string
    {
        return $this->webpImage;
    }

    /**
     * @param string $webpImage
     *
     * @return Picture
     */
    public function setWebpImage(string $webpImage)
    {
        $this->webpImage = $webpImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalImage(): string
    {
        return $this->originalImage;
    }

    /**
     * @param string $originalImage
     *
     * @return Picture
     */
    public function setOriginalImage(string $originalImage)
    {
        $this->originalImage = $originalImage;
        return $this;
    }

    /**
     * @return string
     */
    public function getAltText(): string
    {
        return $this->altText;
    }

    /**
     * @param string $altText
     *
     * @return Picture
     */
    public function setAltText(string $altText)
    {
        $this->altText = $altText;
        return $this;
    }

    /**
     * @return string
     */
    public function getWidth(): string
    {
        return $this->width;
    }

    /**
     * @param string $width
     * @return Picture
     */
    public function setWidth(string $width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string
     */
    public function getHeight(): string
    {
        return $this->height;
    }

    /**
     * @param string $height
     * @return Picture
     */
    public function setHeight(string $height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalTag(): string
    {
        return $this->originalTag;
    }

    /**
     * @param string $originalTag
     *
     * @return Picture
     */
    public function setOriginalTag(string $originalTag)
    {
        $this->originalTag = $originalTag;
        return $this;
    }

    /**
     * @return string
     */
    public function getOriginalImageType(): string
    {
        if (preg_match('/\.(jpg|jpeg)$/i', $this->getOriginalImage())) {
            return 'image/jpg';
        }

        if (preg_match('/\.(png)$/i', $this->getOriginalImage())) {
            return 'image/png';
        }

        return '';
    }

    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }

    /**
     * @param bool $debug
     */
    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @param string $class
     *
     * @return Picture
     */
    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }
}
