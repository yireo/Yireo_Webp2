<?php
declare(strict_types=1);

namespace Yireo\Webp2\Convertor;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
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
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * ConvertWrapper constructor.
     * @param Config $config
     * @param LoggerInterface $logger
     * @param FileDriver $fileDriver
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Config $config,
        LoggerInterface $logger,
        FileDriver $fileDriver,
        DirectoryList $directoryList
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->fileDriver = $fileDriver;
        $this->directoryList = $directoryList;
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
                $this->logger->debug($e->getMessage() . ' - ' . $e->description, ['exception' => $e]);
                continue;
            } catch (\Exception $e) {
                $this->logger->debug($e->getMessage(), ['exception' => $e]);
                continue;
            }

            $converted = true;
            break;
        }

        $this->restore($backupFilename, $destinationImageFilename, $converted);
    }

    private function backup(string $filename): ?string
    {
        try {
            if (!$this->fileDriver->isFile($filename)) {
                return null;
            }

            $backupFolder = $this->directoryList->getPath(DirectoryList::TMP) . '/yireo_webp2';
            $this->fileDriver->createDirectory($backupFolder);
            $backupFilename = $backupFolder . '/' . hash('sha256', $filename) . '.' . uniqid('', true) . '.bak';
            $this->fileDriver->copy($filename, $backupFilename);
        } catch (FileSystemException $exception) {
            $this->logger->warning('Unable to backup WebP image: ' . $exception->getMessage());
            return null;
        }

        return $backupFilename;
    }

    private function restore(?string $backupFilename, string $filename, bool $converted): void
    {
        if ($backupFilename === null) {
            return;
        }

        try {
            if ($converted || $this->fileDriver->isFile($filename)) {
                $this->fileDriver->deleteFile($backupFilename);
                return;
            }

            $this->fileDriver->rename($backupFilename, $filename);
        } catch (FileSystemException $exception) {
            $this->logger->warning('Unable to restore WebP image: ' . $exception->getMessage());
        }
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
