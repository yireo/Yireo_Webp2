<?php
declare(strict_types=1);

namespace Yireo\Webp2\Image;

use Exception as ExceptionAlias;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Logger\Debugger;

/**
 * Class UrlReplacer
 *
 * @package Yireo\Webp2\Image
 */
class UrlReplacer
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
     * @param string $imageUrl
     * @return string
     * @throws ExceptionAlias
     */
    public function getWebpUrlFromImageUrl(string $imageUrl): string
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
}
