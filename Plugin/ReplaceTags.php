<?php
declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Exception as ExceptionAlias;
use Magento\Framework\View\LayoutInterface;
use Magento\Store\Model\StoreManagerInterface;
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
    * @var \Magento\Store\Model\StoreManagerInterface $this->_storeManager
    */
    private $_storeManager;
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
        Config $config,
        StoreManagerInterface $storeManager
    ) {
        $this->convertor = $convertor;
        $this->file = $file;
        $this->debugger = $debugger;
        $this->config = $config;
        $this->_storeManager = $storeManager;
    }

    /**
     * Interceptor of getOutput()
     *
     * @param LayoutInterface $layout
     * @param string $output
     * @return string
     * @throws ExceptionAlias
     */
    public function afterGetOutput(LayoutInterface $layout, string $output): string
    {
        $handles = $layout->getUpdate()->getHandles();
        if (empty($handles)) {
            return $output;
        }

        $skippedHandles = [
            'webp_skip',
            'sales_email_order_invoice_items'
        ];
        if (array_intersect($skippedHandles, $handles)) {
            return $output;
        }

        if ($this->config->enabled() === false) {
            return $output;
        }
        
        
        $regex = '/<([^<]+)\ src=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>/mi';
        $regex_data = '/<([^<]+)\ data-src=\"([^\"]+)\.(png|jpg|jpeg)([^>]+)>/mi';
        if (preg_match_all($regex, $output, $matches, PREG_OFFSET_CAPTURE) === false) {           
            if (preg_match_all($regex_data, $output, $matches, PREG_OFFSET_CAPTURE) === false) {
                return $output;
            }
        }
        
       

       
        $output = $this->getConvertedContent($matches, $output, $layout);
        
         if(preg_match_all($regex_data, $output, $matches, PREG_OFFSET_CAPTURE) === false){
             return $output;            
        }
         $output = $this->getConvertedContent($matches, $output, $layout);
        return $output;
    }
    
    
    private function getConvertedContent($matches, $output, $layout){        
        $baseurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
        $mediaurl = $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);        $accumulatedChange = 0;
        foreach ($matches[0] as $index => $match) {
            $offset = $match[1] + $accumulatedChange;
            $htmlTag = $matches[0][$index][0];
            $imageUrl = $matches[2][$index][0] . '.' . $matches[3][$index][0];

            $pos = strpos($imageUrl, $baseurl);
            $cdn = false;
            if ($pos === false) {    
                $pos1 = strpos($imageUrl, $mediaurl);
                if ($pos1 === false) {       
                    $result = false;
                    continue;
                } else {       
                    $localimagePath = str_replace($mediaurl,'',$imageUrl);
                    $imageUrl = str_replace('media/','',$localimagePath);
                    $imageUrl = $baseurl.'media/'.$imageUrl;       
                    $cdn = true;
                } 
    
            } 

            $webpUrl = $this->file->toWebp($imageUrl);
            $altText = $this->getAttributeText($htmlTag, 'alt');
            $width = $this->getAttributeText($htmlTag, 'width');
            $height = $this->getAttributeText($htmlTag, 'height');
            $class = $this->getAttributeText($htmlTag, 'class');




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
                continue;
            }
            //emirajbbd
            if($cdn){
                $imageUrl = $mediaurl.$localimagePath;
                $webpUrl = $this->file->toWebp($imageUrl);
                
            }
            
            
            
            
            $newHtmlTag = $this->getPictureBlock($layout)
                ->setOriginalImage($imageUrl)
                ->setWebpImage($webpUrl)
                ->setAltText($altText)
                ->setOriginalTag($htmlTag)
                ->setClass($class)
                ->setWidth($width)
                ->setHeight($height)
                ->toHtml();

            $output = substr_replace($output, $newHtmlTag, $offset, strlen($htmlTag));
            $accumulatedChange = $accumulatedChange + (strlen($newHtmlTag) - strlen($htmlTag));
        }
        
        return $output;
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
