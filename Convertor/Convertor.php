<?php declare(strict_types=1);

namespace Yireo\Webp2\Convertor;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Magento\Framework\View\Asset\File\NotFoundException;
use WebPConvert\Convert\Exceptions\ConversionFailed\InvalidInput\InvalidImageTypeException;
use WebPConvert\Convert\Exceptions\ConversionFailedException;
use Yireo\NextGenImages\Convertor\ConvertorInterface;
use Yireo\NextGenImages\Exception\ConvertorException;
use Yireo\NextGenImages\Image\Image;
use Yireo\NextGenImages\Image\ImageFactory;
use Yireo\NextGenImages\Image\TargetImageFactory;
use Yireo\NextGenImages\Util\File;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Exception\InvalidConvertorException;
use WebPConvert\Exceptions\InvalidInput\InvalidImageTypeException as InvalidInputImageTypeException;
use WebPConvert\Exceptions\InvalidInputException;

class Convertor implements ConvertorInterface
{
    private const PROTECTED_MEDIA_FOLDERS = [
        'customer',
        'customer_address',
        'custom_options',
        'downloadable',
        'import',
    ];

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
     * @var ImageFactory
     */
    private $imageFactory;

    /**
     * @var DirectoryList
     */
    private $directoryList;

    /**
     * @var FileDriver
     */
    private $fileDriver;

    /**
     * Convertor constructor.
     * @param Config $config
     * @param File $imageFile
     * @param ConvertWrapper $convertWrapper
     * @param TargetImageFactory $targetImageFactory
     * @param ImageFactory $imageFactory
     * @param DirectoryList $directoryList
     * @param FileDriver $fileDriver
     */
    public function __construct(
        Config $config,
        File $imageFile,
        ConvertWrapper $convertWrapper,
        TargetImageFactory $targetImageFactory,
        ImageFactory $imageFactory,
        DirectoryList $directoryList,
        FileDriver $fileDriver
    ) {
        $this->config = $config;
        $this->imageFile = $imageFile;
        $this->convertWrapper = $convertWrapper;
        $this->targetImageFactory = $targetImageFactory;
        $this->imageFactory = $imageFactory;
        $this->directoryList = $directoryList;
        $this->fileDriver = $fileDriver;
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

        $this->assertSupportedMimetype($image);

        // @todo: https://gitlab.hyva.io/hyva-themes/hyva-compat/magento2-yireo-next-gen-images/-/blob/main/src/Plugin/ConverterPlugin.php#L50

        $webpImage = $this->targetImageFactory->create($image, 'webp');
        $this->convert($image->getPath(), $webpImage->getPath());

        if (!$this->imageFile->fileExists($webpImage->getPath())) {
            throw new ConvertorException('WebP path "'.$webpImage->getPath().'" does not exist after conversion');
        }

        if (!empty($image->getSrcSet())) {
            $webpImage->setSrcSet($this->convertSrcSet($image));
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
        $this->assertAllowedPath($sourceImagePath);
        $this->assertAllowedPath($targetImagePath);

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

    /**
     * @param Image $image
     * @throws ConvertorException
     */
    private function assertSupportedMimetype(Image $image): void
    {
        if (!in_array($image->getMimetype(), ['image/jpeg', 'image/jpg', 'image/png'])) {
            throw new ConvertorException('The mimetype "'.$image->getMimetype().'" is not supported');
        }
    }

    private function convertSrcSet(Image $image): string
    {
        $srcSetImages = explode(',', $image->getSrcSet());
        $webpImageSrcSet = '';
        foreach ($srcSetImages as $srcSetImage) {
            $pieces = explode(' ', trim($srcSetImage, " \n\r\t\v\0"));
            $imageUrl = $pieces[0];
            $descriptor = $this->getValidDescriptor($pieces[1] ?? '');

            try {
                $srcSetImage = $this->imageFactory->createFromUrl($imageUrl);
                $this->assertSupportedMimetype($srcSetImage);
                $srcSetWebpImage = $this->targetImageFactory->create($srcSetImage, 'webp');
            } catch (NotFoundException|FileSystemException $exception) {
                throw new ConvertorException('Unable to convert srcset image: '.$exception->getMessage());
            }

            $this->convert($srcSetImage->getPath(), $srcSetWebpImage->getPath());

            if (!$this->imageFile->fileExists($srcSetWebpImage->getPath())) {
                throw new ConvertorException('WebP path "'.$srcSetWebpImage->getPath().'" does not exist after conversion');
            }

            $webpImageSrcSet .= ($webpImageSrcSet ? ', ' : '') . $srcSetWebpImage->getUrl() . ' ' . $descriptor;
        }

        return $webpImageSrcSet;
    }

    private function getValidDescriptor(string $descriptor): string
    {
        if (preg_match('/^\d+(\.\d+)?[wx]$/', $descriptor)) {
            return $descriptor;
        }

        return '';
    }

    /**
     * @param string $path
     * @throws ConvertorException
     */
    private function assertAllowedPath(string $path): void
    {
        try {
            $rootPubFolder = $this->resolvePath($this->directoryList->getRoot() . '/pub');
            $pubFolder = $this->resolvePath($this->directoryList->getPath(DirectoryList::PUB));
            $mediaFolder = $this->resolvePath($this->directoryList->getPath(DirectoryList::MEDIA));
            $staticFolder = $this->resolvePath($this->directoryList->getPath(DirectoryList::STATIC_VIEW));
        } catch (FileSystemException $exception) {
            throw new ConvertorException($exception->getMessage());
        }

        $folder = $this->resolvePath(dirname($path));

        foreach (array_unique([$mediaFolder, $pubFolder.'/media', $rootPubFolder.'/media']) as $protectedRoot) {
            foreach (self::PROTECTED_MEDIA_FOLDERS as $protectedFolder) {
                if ($this->isInFolder($folder, $protectedRoot.'/'.$protectedFolder)) {
                    throw new ConvertorException('Path "'.$path.'" is inside a protected media folder');
                }
            }
        }

        foreach ([$rootPubFolder, $pubFolder, $mediaFolder, $staticFolder] as $allowedFolder) {
            if ($this->isInFolder($folder, $allowedFolder)) {
                return;
            }
        }

        throw new ConvertorException('Path "'.$path.'" is outside of the media and static folders');
    }

    private function isInFolder(string $path, string $folder): bool
    {
        return $path === $folder || str_starts_with($path, $folder.'/');
    }

    private function resolvePath(string $path): string
    {
        $normalizedPath = $this->normalizePath($path);
        $missingSegments = [];
        $currentPath = $normalizedPath;
        while ($currentPath !== '/') {
            $realPath = $this->fileDriver->getRealPath($currentPath);
            if (is_string($realPath) && $realPath !== '') {
                array_unshift($missingSegments, rtrim($realPath, '/'));
                return implode('/', $missingSegments);
            }

            array_unshift($missingSegments, basename($currentPath));
            $currentPath = dirname($currentPath);
        }

        return $normalizedPath;
    }

    private function normalizePath(string $path): string
    {
        $segments = [];
        foreach (explode('/', str_replace('\\', '/', $path)) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }

            if ($segment === '..') {
                array_pop($segments);
                continue;
            }

            $segments[] = $segment;
        }

        return '/'.implode('/', $segments);
    }
}
