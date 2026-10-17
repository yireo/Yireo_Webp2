import {test, expect, configureWebp, disableWebpSupport, expectBodyClass} from '@yireo-webp2/webp';

test.describe('WebP body class', function () {
    test.beforeEach(async function ({page}) {
        await configureWebp(page);
    });

    test('adds the class "webp" to the body when the browser supports WebP', async function ({page, pageErrors}) {
        await page.goto('/');

        await expectBodyClass(page, 'webp');
        await expect(page.locator('body')).not.toHaveClass(/(^|\s)no-webp(\s|$)/);
        expect(pageErrors).toEqual([]);
    });

    test('adds the class "no-webp" to the body when the browser lacks WebP support', async function ({page}) {
        await disableWebpSupport(page);
        await page.goto('/');

        await expectBodyClass(page, 'no-webp');
        await expect(page.locator('body')).not.toHaveClass(/(^|\s)webp(\s|$)/);
    });

    test('loads the detection script via RequireJS', async function ({page}) {
        await page.goto('/');
        await expectBodyClass(page, 'webp');

        const hasWebp = await page.evaluate(() => new Promise(resolve => {
            (window as any).require(['Yireo_Webp2/js/has-webp'], (hasWebP: () => boolean) => resolve(hasWebP()));
        }));
        expect(hasWebp).toBe(true);
    });
});
