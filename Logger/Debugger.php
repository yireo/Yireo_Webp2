<?php
declare(strict_types=1);

namespace Yireo\Webp2\Logger;

use Psr\Log\LoggerInterface;
use Yireo\Webp2\Config\Config;

/**
 * Class Debugger
 *
 * @package Yireo\Webp2\Logger
 */
class Debugger
{
    /**
     * @var Config
     */
    private $config;
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Debugger constructor.
     *
     * @param Config $config
     * @param LoggerInterface $logger
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param string $msg
     * @param array $data
     *
     * @return bool
     */
    public function debug(string $msg, $data = null): bool
    {
        if ($this->config->isDebugging() === false) {
            return false;
        }

        if (!empty($data)) {
            $msg .= ': '.var_export($data, true);
        }

        $this->logger->notice($msg);
        return true;
    }
}
