<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\TestFramework\TestCase\AbstractController;
use RuntimeException;
use Yireo\Webp2\Image\ConvertWrapper;
use Yireo\Webp2\Test\Utils\ImageProvider;
use Yireo\Webp2\Test\Utils\ConvertWrapperStub;

/**
 * Class Common
 * @package Yireo\Webp2\Test\Integration
 */
class Common extends AbstractController
{
    protected function setUp()
    {
        parent::setUp();
        $this->_objectManager->addSharedInstance($this->_objectManager->get(ConvertWrapperStub::class), ConvertWrapper::class);
    }

    protected function fixtureImageFiles()
    {
        /** @var DirectoryList $directoryList */
        $directoryList = $this->_objectManager->get(DirectoryList::class);
        $root = $directoryList->getRoot();
        $imagesInThemePath = $root . '/pub/static/frontend/Magento/luma/en_US/Yireo_Webp2/images/test';

        if (!is_dir($imagesInThemePath)) {
            mkdir($imagesInThemePath, 0777, true);
        }

        if (!is_dir($imagesInThemePath)) {
            throw new RuntimeException('Failed to create folder: ' . $imagesInThemePath);
        }

        $currentFiles = scandir($imagesInThemePath);
        foreach ($currentFiles as $currentFile) {
            if (!in_array($currentFile, ['.', '..'])) {
                unlink($imagesInThemePath . '/' . $currentFile);
            }
        }

        /** @var ImageProvider $imageProvider */
        $imageProvider = $this->_objectManager->get(ImageProvider::class);
        $images = $imageProvider->getImages();

        /** @var ComponentRegistrar $componentRegistrar */
        $componentRegistrar = $this->_objectManager->get(ComponentRegistrar::class);
        $modulePath = $componentRegistrar->getPath('module', 'Yireo_Webp2');
        $moduleWebPath = $modulePath . '/view/frontend/web';

        foreach ($images as $image) {
            $sourceImage = $moduleWebPath . '/' . $image;
            $destinationImage = $imagesInThemePath . '/' . basename($image);
            copy($sourceImage, $destinationImage);
        }
    }

    /**
     * @return ImageProvider
     */
    protected function getImageProvider(): ImageProvider
    {
        return $this->_objectManager->get(ImageProvider::class);
    }

    /**
     * @param string $body
     */
    protected function assertImageTagsExist(string $body, $images)
    {
        foreach ($images as $image) {
            $webPImage = preg_replace('/\.(png|jpg)$/', '.webp', $image);
            $this->assertContains($webPImage, $body);
        }
    }
}
