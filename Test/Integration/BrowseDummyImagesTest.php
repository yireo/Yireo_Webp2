<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Module\ModuleListInterface;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\TestCase\AbstractController;
use RuntimeException;
use Yireo\Webp2\Test\ImageProvider;

/**
 * Class BrowseDummyImagesTest
 * @package Yireo\Webp2\Test\Integration
 */
class BrowseDummyImagesTest extends AbstractController
{
    /**
     * @magentoAdminConfigFixture system/yireo_webp/enabled 1
     * @magentoAdminConfigFixture system/yireo_webp/debug 1
     */
    public function testIfHtmlContainsWebpImages()
    {
        $this->fixtureImageFiles();

        $this->getResponse()->setHeader('Accept', 'image/webp');
        $this->dispatch('webp/test/images/case/multiple');

        /** @var LayoutInterface $layout */
        $layout = $this->_objectManager->get(LayoutInterface::class);
        $body = $layout->getOutput();

        $this->assertImageTagsExist($body);
    }

    private function fixtureImageFiles()
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
     * @param string $body
     */
    private function assertImageTagsExist(string $body)
    {
        $images = $this->getImageProvider()->getImages();
        foreach ($images as $image) {
            $webPImage = preg_replace('/\.(png|jpg)$/', '.webp', $image);
            $this->assertContains($webPImage, $body);
        }
    }

    /**
     * @return ImageProvider
     */
    private function getImageProvider(): ImageProvider
    {
        return $this->_objectManager->get(ImageProvider::class);
    }
}
