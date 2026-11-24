<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Driver\File as FileDriver;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Convertor\ConvertWrapper;
use Yireo\Webp2\Exception\InvalidConvertorException;

class ConvertWrapperTest extends TestCase
{
    private string $tmpDir = '';

    private string $backupDir = '';

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/yireo-webp2-' . uniqid();
        $this->backupDir = sys_get_temp_dir() . '/yireo-webp2-var-tmp-' . uniqid();
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        foreach ([$this->tmpDir, $this->backupDir . '/yireo_webp2', $this->backupDir] as $folder) {
            if (!is_dir($folder)) {
                continue;
            }

            foreach (glob($folder . '/*') as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }

            rmdir($folder);
        }
    }

    private function getConvertWrapper(Config $config, ?LoggerInterface $logger = null): ConvertWrapper
    {
        $directoryList = $this->createMock(DirectoryList::class);
        $directoryList->method('getPath')->willReturnMap([[DirectoryList::TMP, $this->backupDir]]);

        return new ConvertWrapper(
            $config,
            $logger ?? $this->createMock(LoggerInterface::class),
            new FileDriver(),
            $directoryList
        );
    }

    public function testGetOptions()
    {
        $config = $this->createMock(Config::class);
        $config->method('getQualityLevel')->willReturn(75);
        $config->method('getEncoding')->willReturn('lossless');

        $convertWrapper = $this->getConvertWrapper($config);
        $this->assertSame([
            'quality' => 'auto',
            'max-quality' => 75,
            'encoding' => 'lossless',
        ], $convertWrapper->getOptions());
    }

    public function testGetOptionsThrowsExceptionOnInvalidEncoding()
    {
        $config = $this->createMock(Config::class);
        $config->method('getEncoding')->willThrowException(new InvalidConvertorException('Invalid encoding'));

        $convertWrapper = $this->getConvertWrapper($config);
        $this->expectException(InvalidConvertorException::class);
        $convertWrapper->getOptions();
    }

    public function testConvertTriesEveryConvertorAndLogsFailures()
    {
        $config = $this->createMock(Config::class);
        $config->method('getQualityLevel')->willReturn(80);
        $config->method('getEncoding')->willReturn('lossy');
        $config->method('getConvertors')->willReturn(['gd', 'imagick']);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->exactly(2))->method('debug');

        $convertWrapper = $this->getConvertWrapper($config, $logger);
        $convertWrapper->convert($this->tmpDir . '/non-existing.jpg', $this->tmpDir . '/non-existing.webp');
        $this->assertFileDoesNotExist($this->tmpDir . '/non-existing.webp');
    }

    public function testConvertKeepsExistingWebpImageWhenAllConvertorsFail()
    {
        $source = $this->tmpDir . '/source.jpg';
        $destination = $this->tmpDir . '/source.webp';
        file_put_contents($source, 'not an image');
        file_put_contents($destination, 'existing webp');

        $config = $this->createMock(Config::class);
        $config->method('getQualityLevel')->willReturn(80);
        $config->method('getEncoding')->willReturn('lossy');
        $config->method('getConvertors')->willReturn(['gd', 'wpc']);

        $convertWrapper = $this->getConvertWrapper($config);
        $convertWrapper->convert($source, $destination);

        $this->assertFileExists($destination);
        $this->assertSame('existing webp', file_get_contents($destination));
        $this->assertSame(['source.jpg', 'source.webp'], array_map('basename', glob($this->tmpDir . '/*')));
        $this->assertSame([], glob($this->backupDir . '/yireo_webp2/*'));
    }

    public function testConvertDoesNotFailWhenBackupFolderIsNotWritable()
    {
        $source = $this->tmpDir . '/source.jpg';
        $destination = $this->tmpDir . '/source.webp';
        file_put_contents($source, 'not an image');
        file_put_contents($destination, 'existing webp');
        file_put_contents($this->backupDir, 'a file instead of a folder');

        $config = $this->createMock(Config::class);
        $config->method('getQualityLevel')->willReturn(80);
        $config->method('getEncoding')->willReturn('lossy');
        $config->method('getConvertors')->willReturn(['gd']);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())->method('warning')
            ->with($this->stringStartsWith('Unable to backup WebP image'));

        $this->getConvertWrapper($config, $logger)->convert($source, $destination);
        unlink($this->backupDir);

        $this->assertSame(['source.jpg'], array_map('basename', glob($this->tmpDir . '/*')));
    }

    public function testConvertCreatesWebpImageWithGd()
    {
        if (!function_exists('imagewebp') || !function_exists('imagejpeg')) {
            $this->markTestSkipped('GD does not support JPEG and WebP');
        }

        $source = $this->tmpDir . '/source.jpg';
        $destination = $this->tmpDir . '/source.webp';
        $gdImage = imagecreatetruecolor(20, 20);
        imagefill($gdImage, 0, 0, (int)imagecolorallocate($gdImage, 255, 0, 0));
        imagejpeg($gdImage, $source);

        $config = $this->createMock(Config::class);
        $config->method('getQualityLevel')->willReturn(80);
        $config->method('getEncoding')->willReturn('lossy');
        $config->method('getConvertors')->willReturn(['gd']);

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->never())->method('debug');

        $convertWrapper = $this->getConvertWrapper($config, $logger);
        $convertWrapper->convert($source, $destination);

        $this->assertFileExists($destination);
        $this->assertSame('image/webp', mime_content_type($destination));
    }
}
