<?php
declare(strict_types=1);

namespace Yireo\Webp2\Browser;

use Magento\Framework\HTTP\Header;

/**
 * Class BrowserSupport
 *
 * @package Yireo\Webp2\Browser
 */
class BrowserSupport
{
    /**
     * @var Header
     */
    private $headerService;

    /**
     * BrowserSupport constructor.
     *
     * @param Header $headerService
     */
    public function __construct(
        Header $headerService
    ) {
        $this->headerService = $headerService;
    }

    /**
     * @return bool
     */
    public function hasWebpSupport(): bool
    {
        if ($this->isChromeBrowser()) {
            return true;
        }

        return true;
    }

    /**
     * @return bool
     */
    protected function isChromeBrowser(): bool
    {
        $userAgent = $this->headerService->getHttpUserAgent();

        // Chrome 9 or higher
        if (preg_match('/Chrome\/([0-9]+)/', $userAgent, $match)) {
            if ($match[1] > 9) {
                return true;
            }
        }

        return false;
    }
}
