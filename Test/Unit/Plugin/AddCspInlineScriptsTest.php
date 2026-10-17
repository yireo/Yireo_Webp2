<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Unit\Plugin;

use Magento\Framework\View\Element\Template;
use PHPUnit\Framework\TestCase;
use Yireo\CspUtilities\Util\ReplaceInlineScripts;
use Yireo\Webp2\Plugin\AddCspInlineScripts;

class AddCspInlineScriptsTest extends TestCase
{
    public function testAfterToHtmlIgnoresOtherBlocks()
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->never())->method('replace');

        $plugin = new AddCspInlineScripts($replaceInlineScripts);
        $html = '<script>alert(1)</script>';
        $this->assertSame($html, $plugin->afterToHtml($this->getBlock('foo.bar'), $html));
        $this->assertSame($html, $plugin->afterToHtml($this->getBlock(null), $html));
    }

    public function testAfterToHtmlReplacesInlineScriptsOfWebp2Blocks()
    {
        $replaceInlineScripts = $this->createMock(ReplaceInlineScripts::class);
        $replaceInlineScripts->expects($this->once())
            ->method('replace')
            ->with('<script>alert(1)</script>')
            ->willReturn('<script nonce="foobar">alert(1)</script>');

        $plugin = new AddCspInlineScripts($replaceInlineScripts);
        $block = $this->getBlock('yireo_webp2.add-webp-class-to-body');
        $this->assertSame(
            '<script nonce="foobar">alert(1)</script>',
            $plugin->afterToHtml($block, '<script>alert(1)</script>')
        );
    }

    public function testAfterToHtmlCastsNullToString()
    {
        $plugin = new AddCspInlineScripts($this->createMock(ReplaceInlineScripts::class));
        $this->assertSame('', $plugin->afterToHtml($this->getBlock('foo.bar'), null));
    }

    private function getBlock(?string $nameInLayout): Template
    {
        $block = $this->createMock(Template::class);
        $block->method('getNameInLayout')->willReturn($nameInLayout);
        return $block;
    }
}
