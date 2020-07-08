<?php
declare(strict_types=1);

namespace Yireo\Webp2\Plugin;

use Exception as ExceptionAlias;
use Magento\Framework\View\LayoutInterface;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Image\HtmlReplacer;

class ReplaceTagsPlugin
{
    /**
     * @var HtmlReplacer
     */
    private $htmlReplacer;

    /**
     * @var Config
     */
    private $config;

    /**
     * ReplaceTags constructor.
     *
     * @param HtmlReplacer $htmlReplacer
     * @param Config $config
     */
    public function __construct(
        HtmlReplacer $htmlReplacer,
        Config $config
    ) {
        $this->htmlReplacer = $htmlReplacer;
        $this->config = $config;
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
        if ($this->shouldModifyOutput($layout) === false) {
            return $output;
        }

        return $this->htmlReplacer->replaceImagesInHtml($layout, $output);
    }

    /**
     * @param LayoutInterface $layout
     * @return bool
     */
    private function shouldModifyOutput(LayoutInterface $layout): bool
    {
        $handles = $layout->getUpdate()->getHandles();
        if (empty($handles)) {
            return false;
        }

        $skippedHandles = [
            'webp_skip',
            'sales_email_order_invoice_items'
        ];

        if (array_intersect($skippedHandles, $handles)) {
            return false;
        }

        if ($this->config->enabled() === false) {
            return false;
        }

        return true;
    }
}
