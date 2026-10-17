<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Convertor;

use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Yireo\Webp2\Config\Config;
use Yireo\Webp2\Convertor\ConvertWrapper;
use Yireo\Webp2\Exception\InvalidConvertorException;

class ConvertWrapperTest extends TestCase
{
    private string $tmpDir = '';

    protected function setUp(): void
    {
        $this->tmpDir = sys_get_temp_dir() . '/yireo-webp2-' . uniqid();
        mkdir($this->tmpDir);
    }

    protected function tearDown(): void
    {
        foreach (glob($this->tmpDir . '/*') as $file) {
            unlink($file);
        }

        rmdir($this->tmpDir);
    }

    public function testGetOptions()
    {
        $config = $this->createMock(Config::class);
        $config->method('getQualityLevel')->willReturn(75);
        $config->method('getEncoding')->willReturn('lossless');

        $convertWrapper = new ConvertWrapper($config, $this->createMock(LoggerInterface::class));
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

        $convertWrapper = new ConvertWrapper($config, $this->createMock(LoggerInterface::class));
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

        $convertWrapper = new ConvertWrapper($config, $logger);
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

        $convertWrapper = new ConvertWrapper($config, $this->createMock(LoggerInterface::class));
        $convertWrapper->convert($source, $destination);

        $this->assertFileExists($destination);
        $this->assertSame('existing webp', file_get_contents($destination));
        $this->assertSame(['source.jpg', 'source.webp'], array_map('basename', glob($this->tmpDir . '/*')));
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

        $convertWrapper = new ConvertWrapper($config, $logger);
        $convertWrapper->convert($source, $destination);

        $this->assertFileExists($destination);
        $this->assertSame('image/webp', mime_content_type($destination));
    }
}
