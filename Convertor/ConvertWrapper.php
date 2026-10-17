<?php
declare(strict_types=1);

namespace Yireo\Webp2\Convertor;

use Exception;
use Psr\Log\LoggerInterface;
use WebPConvert\Convert\Exceptions\ConversionFailed\InvalidInput\InvalidImageTypeException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use WebPConvert\WebPConvert;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Exception\InvalidConvertorException;

/**
 * Class ConvertWrapper to wrap third party wrapper for purpose of preference overrides and testing
 */
class ConvertWrapper
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
     * ConvertWrapper constructor.
     * @param Config $config
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger
    ) {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param string $sourceImageFilename
     * @param string $destinationImageFilename
     * @throws ConversionFailedException
     * @throws InvalidConvertorException
     * @throws InvalidImageTypeException
     */
    public function convert(string $sourceImageFilename, string $destinationImageFilename): void
    {
        // WebPConvert removes the destination when a conversion fails, so keep a backup of an existing WebP image
        $backupFilename = $this->backup($destinationImageFilename);
        $converted = false;

        $options = $this->getOptions();
        foreach ($this->config->getConvertors() as $convertor){
            $options['converter'] = $convertor;
            try {
                WebPConvert::convert($sourceImageFilename, $destinationImageFilename, $options);
            } catch (ConversionFailedException $e) {
                $this->logger->debug($e->getMessage() . ' - ' . $e->description, $e->getTrace());
                continue;
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage(), $e->getTrace());
                continue;
            }

            $converted = true;
            break;
        }

        $this->restore($backupFilename, $destinationImageFilename, $converted);
    }

    private function backup(string $filename): ?string
    {
        if (!is_file($filename)) {
            return null;
        }

        $backupFilename = $filename . '.' . uniqid() . '.bak';
        if (!copy($filename, $backupFilename)) {
            return null;
        }

        return $backupFilename;
    }

    private function restore(?string $backupFilename, string $filename, bool $converted): void
    {
        if ($backupFilename === null) {
            return;
        }

        if ($converted || is_file($filename)) {
            unlink($backupFilename);
            return;
        }

        rename($backupFilename, $filename);
    }

    /**
     * @return array
     * @throws InvalidConvertorException
     */
    public function getOptions(): array
    {
        return [
            'quality' => 'auto',
            'max-quality' => $this->config->getQualityLevel(),
            'encoding' => $this->config->getEncoding(),
        ];
    }
}
