<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Plugin;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class AddCspInlineScriptsTest extends TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store csp_utilities/settings/inline_script_mode nonce
     */
    public function testIfInlineScriptOfWebp2BlockGetsNonce()
    {
        $html = $this->renderBlock('yireo_webp2.add-webp-class-to-body');
        $this->assertStringContainsString('function hasWebP()', $html);
        $this->assertMatchesRegularExpression('/<script nonce="[^"]+"/', $html);
    }

    /**
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store csp_utilities/settings/inline_script_mode nonce
     */
    public function testIfInlineScriptOfOtherBlockIsUntouchedByWebp2()
    {
        $html = $this->renderBlock('foobar.add-webp-class-to-body');
        $this->assertStringContainsString('function hasWebP()', $html);
        $this->assertStringContainsString('<script>', $html);
        $this->assertStringNotContainsString('nonce=', $html);
    }

    private function renderBlock(string $name): string
    {
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, $name);
        $block->setTemplate('Yireo_Webp2::hyva/add-webp-class-to-body.phtml');
        return (string)$block->toHtml();
    }
}
