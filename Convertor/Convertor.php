<?php declare(strict_types=1);

namespace Yireo\Webp2\Convertor;

use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use WebPConvert\Convert\Exceptions\ConversionFailed\InvalidInput\InvalidImageTypeException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use Yireo\NextGenImages\Convertor\ConvertorInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\TargetImageFactory;
use Yireo\NextGenImages\Util\File;
use Yireo\NextGenImages\Image\Image;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Exception\InvalidConvertorException;
use WebPConvert\Exceptions\InvalidInput\InvalidImageTypeException as InvalidInputImageTypeException;
use WebPConvert\Exceptions\InvalidInputException;

class Convertor implements ConvertorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var File
     */
    private $imageFile;

    /**
     * @var ConvertWrapper
     */
    private $convertWrapper;

    /**
     * @var TargetImageFactory
     */
    private $targetImageFactory;

    /**
     * Convertor constructor.
     * @param Config $config
     * @param File $imageFile
     * @param ConvertWrapper $convertWrapper
     * @param TargetImageFactory $targetImageFactory
     */
    public function __construct(
        Config $config,
        File $imageFile,
        ConvertWrapper $convertWrapper,
        TargetImageFactory $targetImageFactory
    ) {
        $this->config = $config;
        $this->imageFile = $imageFile;
        $this->convertWrapper = $convertWrapper;
        $this->targetImageFactory = $targetImageFactory;
    }

    /**
     * @param Image $image
     * @return Image
     * @throws ConvertorException
     * @throws FileSystemException
     */
    public function convertImage(Image $image): Image
    {
        if (!$this->config->enabled()) {
            throw new ConvertorException('WebP conversion is not enabled');
        }

        if (!in_array($image->getMimetype(), ['image/jpeg', 'image/jpg', 'image/png'])) {
            throw new ConvertorException('The mimetype "'.$image->getMimetype().'" is not supported');
        }

        // @todo: https://gitlab.hyva.io/hyva-themes/hyva-compat/magento2-yireo-next-gen-images/-/blob/main/src/Plugin/ConverterPlugin.php#L50

        $webpImage = $this->targetImageFactory->create($image, 'webp');
        $result = $this->convert($image->getPath(), $webpImage->getPath());

        if (!$result && !$this->imageFile->fileExists($webpImage->getPath())) {
            throw new ConvertorException('WebP path "'.$webpImage->getPath().'" does not exist after conversion');
        }

        return $webpImage;
    }

    /**
     * @param string $sourceImagePath
     * @param string $targetImagePath
     * @return bool
     * @throws ConvertorException
     */
    private function convert(string $sourceImagePath, string $targetImagePath): bool
    {
        if (!$this->imageFile->fileExists($sourceImagePath)) {
            throw new ConvertorException('Source cached image does not exists: '.$sourceImagePath);
        }

        if (!$this->imageFile->needsConversion($sourceImagePath, $targetImagePath)) {
            return true;
        }

        if (!$this->config->enabled() || !$this->config->allowImageCreation()) {
            throw new ConvertorException('WebP conversion is not enabled');
        }

        try {
            $this->convertWrapper->convert($sourceImagePath, $targetImagePath);
        } catch (InvalidImageTypeException|InvalidInputException|InvalidInputImageTypeException $e) {
            return false;
        } catch (ConversionFailedException|InvalidConvertorException $e) {
            throw new ConvertorException($targetImagePath.': '.$e->getMessage());
        }

        return true;
    }
}
