<?php
declare(strict_types=1);

namespace Yireo\Webp2\Browser;

/**
 * Class BrowserSupport
 *
 * @package Yireo\Webp2\Browser
 */
class BrowserSupport
{
    public function __construct(

    ) {
    }

    /**
     * @return bool
     */
    public function hasWebpSupport(): bool
    {
        return true;
    }
}
