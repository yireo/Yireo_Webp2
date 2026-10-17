import {
    test,
    expect,
    configureHyvaWebp,
    disableWebpSupport,
    expectBodyClass,
    gotoAndGetCspNonces,
} from '@yireo-webp2/webp';

/**
 * Hyva variant of webp-body-class-test.spec.ts: With Hyva, the class is added by an inline script instead of RequireJS
 */

test.describe('WebP body class (Hyva)', function () {
    test.beforeEach(async function ({page}) {
        await configureHyvaWebp(page);
    });

    test('adds the class "webp" to the body when the browser supports WebP', async function ({page, pageErrors}) {
        await page.goto('/');

        await expectBodyClass(page, 'webp');
        await expect(page.locator('body')).not.toHaveClass(/(^|\s)no-webp(\s|$)/);
        expect(await page.evaluate(() => typeof (window as any).require)).toBe('undefined');
        expect(pageErrors).toEqual([]);
    });

    test('adds the class "no-webp" to the body when the browser lacks WebP support', async function ({page}) {
        await disableWebpSupport(page);
        await page.goto('/');

        await expectBodyClass(page, 'no-webp');
        await expect(page.locator('body')).not.toHaveClass(/(^|\s)webp(\s|$)/);
    });

    test('adds a CSP nonce to the inline script', async function ({page}) {
        const allowedNonces = await gotoAndGetCspNonces(page, '/');
        await expectBodyClass(page, 'webp');

        const scriptNonces = await page.locator('script').evaluateAll((scripts: HTMLScriptElement[]) => scripts
            .filter(script => (script.textContent ?? '').includes('function hasWebP()'))
            .map(script => script.nonce)
        );

        expect(scriptNonces).toHaveLength(1);
        expect(scriptNonces[0], 'Nonce of the inline script').not.toBe('');

        // The storefront policy only lists nonces when it does not allow 'unsafe-inline'
        if (allowedNonces.length > 0) {
            expect(allowedNonces).toContain(scriptNonces[0]);
        }
    });
});
