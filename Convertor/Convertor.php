<?php declare(strict_types=1);

namespace Yireo\Webp2\Convertor;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\Filesystem\File\ReadFactory as FileReadFactory;
use Magento\Framework\View\Asset\File\NotFoundException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use Yireo\NextGenImages\Convertor\ConvertorInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\File;
use Yireo\NextGenImages\Image\SourceImage;
use Yireo\NextGenImages\Image\SourceImageFactory;
use Yireo\NextGenImages\Logger\Debugger;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Image\ConvertWrapper;

class Convertor implements ConvertorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var SourceImageFactory
     */
    private $sourceImageFactory;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @var ConvertWrapper
     */
    private $convertWrapper;

    /**
     * @var FileReadFactory
     */
    private $fileReadFactory;

    /**
     * @var Debugger
     */
    private $debugger;

    /**
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * Convertor constructor.
     * @param Config $config
     * @param SourceImageFactory $sourceImageFactory
     * @param File $imageFile
     * @param ConvertWrapper $convertWrapper
     * @param FileReadFactory $fileReadFactory
     * @param Debugger $debugger
     * @param FileDriver $fileDriver
     */
    public function __construct(
        Config $config,
        SourceImageFactory $sourceImageFactory,
        File $imageFile,
        ConvertWrapper $convertWrapper,
        FileReadFactory $fileReadFactory,
        Debugger $debugger,
        FileDriver $fileDriver
    ) {
        $this->config = $config;
        $this->sourceImageFactory = $sourceImageFactory;
        $this->imageFile = $imageFile;
        $this->convertWrapper = $convertWrapper;
        $this->fileReadFactory = $fileReadFactory;
        $this->debugger = $debugger;
        $this->fileDriver = $fileDriver;
    }

    /**
     * @param string $imageUrl
     * @return SourceImage
     * @throws ConvertorException
     * @throws FileSystemException
     * @deprecated Use getSourceImage() instead
     */
    public function convertByUrl(string $imageUrl): SourceImage
    {
        return $this->getSourceImage($imageUrl);
    }

    /**
     * @param string $imageUrl
     * @return SourceImage
     * @throws ConvertorException
     * @throws FileSystemException
     */
    public function getSourceImage(string $imageUrl): SourceImage
    {
        if (!$this->config->enabled()) {
            throw new ConvertorException('WebP conversion is not enabled');
        }

        $webpUrl = $this->imageFile->convertSuffix($imageUrl, '.webp');
        $result = $this->convert($imageUrl, $webpUrl);

        if (!$result && !$this->imageFile->uriExists($webpUrl)) {
            throw new ConvertorException('WebP URL "' . $webpUrl . '" does not exist after conversion');
        }

        return $this->sourceImageFactory->create(['url' => $webpUrl, 'mimeType' => 'image/webp']);
    }

    /**
     * @param string $sourceImageUri
     * @param string|null $destinationImageUri
     * @return bool
     * @throws ConvertorException
     * @throws FileSystemException
     */
    public function convert(string $sourceImageUri, ?string $destinationImageUri = null): bool
    {
        if (!$destinationImageUri) {
            $destinationImageUri = preg_replace('/\.(jpg|jpeg|png|gif)$/', '.webp', $sourceImageUri);
        }

        $sourceImageFilename = $this->imageFile->resolve($sourceImageUri);
        $destinationImageFilename = $this->imageFile->resolve($destinationImageUri);

        if (!$this->imageFile->needsConversion($sourceImageFilename, $destinationImageFilename)) {
            throw new ConvertorException('No conversion needed');
        }

        if (!$this->config->enabled()) {
            throw new ConvertorException('WebP conversion is not enabled');
        }

        try {
            $this->convertWrapper->convert($sourceImageFilename, $destinationImageFilename);
        } catch (ConversionFailedException $e) {
            throw new ConvertorException($destinationImageFilename . ': ' . $e->getMessage());
        }

        return true;
    }
}
