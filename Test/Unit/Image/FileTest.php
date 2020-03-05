<?php

use Magento\Framework\Filesystem\Directory\ReadFactory;
use Magento\Framework\Filesystem\DirectoryList;
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
    public function testResolve()
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

        $readFactoryMock = $this->getMockBuilder(ReadFactory::class)
            ->disableOriginalConstructor()
            ->getMock();

        $target = new File($directoryListMock, $readFactoryMock);
        return $target;
    }
}
