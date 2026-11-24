<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Template;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\LayoutInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * @magentoAppArea frontend
 */
class InlineScriptTest extends TestCase
{
    /**
     * @dataProvider getTemplates
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store csp/policies/storefront/scripts/inline 0
     */
    #[DataProvider('getTemplates')]
    public function testIfInlineScriptGetsNonceWithStrictPolicy(string $template, string $expectedScript)
    {
        $html = $this->renderTemplate($template);
        $this->assertStringContainsString($expectedScript, $html);
        $this->assertMatchesRegularExpression('/<script nonce="[^"]+"/', $html);
    }

    /**
     * @dataProvider getTemplates
     * @magentoAppIsolation enabled
     * @magentoConfigFixture current_store csp/policies/storefront/scripts/inline 1
     */
    #[DataProvider('getTemplates')]
    public function testIfInlineScriptIsRenderedWithInlinePolicy(string $template, string $expectedScript)
    {
        $html = $this->renderTemplate($template);
        $this->assertStringContainsString($expectedScript, $html);
        $this->assertSame(1, substr_count($html, '<script'));
    }

    public static function getTemplates(): array
    {
        return [
            ['Yireo_Webp2::hyva/add-webp-class-to-body.phtml', 'function hasWebP()'],
            ['Yireo_Webp2::hyva/gallery-additions.phtml', 'yireoWebp2ReplaceImage'],
        ];
    }

    private function renderTemplate(string $template): string
    {
        $layout = Bootstrap::getObjectManager()->get(LayoutInterface::class);
        $block = $layout->createBlock(Template::class, 'foobar.' . uniqid());
        $block->setTemplate($template);
        return (string)$block->toHtml();
    }
}
