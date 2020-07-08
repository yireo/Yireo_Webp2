<?php
declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Image;

use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\ReadFactory as DirectoryReadFactory;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use Yireo\Webp2\Image\File;
use PHPUnit\Framework\TestCase;

/**
 * Class FileTest testing behaviour of File
 */
class FileTest extends TestCase
{
    /**
     * @test Test the resolve() function
     */
    public function testResolve(): void
    {
        $target = $this->getTarget();
        $resolvedFile = $target->resolve('http://anotherhost.com/some/fake/url.png');
        $this->assertSame($resolvedFile, '/pub/some/fake/url.png');
    }

    /**
     * @return File
     */
    public function getTarget(): File
    {
        $directoryListMock = $this->getMockBuilder(DirectoryList::class)
            ->disableOriginalConstructor()
            ->getMock();

        $directoryReadFactoryMock = $this->getMockBuilder(DirectoryReadFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileDriverMock = $this->getMockBuilder(FileDriver::class)
            ->disableOriginalConstructor()
            ->getMock();

        $target = new File($directoryListMock, $directoryReadFactoryMock, $fileDriverMock); // phpstan:ignore
        return $target;
    }
}
