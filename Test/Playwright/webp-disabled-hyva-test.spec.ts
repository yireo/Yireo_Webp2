import {test, expect, configureHyvaWebp, gotoTestCase, getWebpSources, TEST_IMAGES} from '@yireo-webp2/webp';

/**
 * Hyva variant of webp-disabled-test.spec.ts
 */

test.describe('WebP disabled (Hyva)', function () {
    test('does not add WebP sources when disabled', async function ({page}) {
        await configureHyvaWebp(page, {config: {'yireo_webp2/settings/enabled': 0}});

        const webpRequests: string[] = [];
        page.on('request', request => {
            if (request.url().endsWith('.webp')) {
                webpRequests.push(request.url());
            }
        });

        await gotoTestCase(page, 'multiple_images');

        await expect(page.locator('img[src*="/Yireo_Webp2/images/test/"]')).toHaveCount(TEST_IMAGES.length);
        expect(await getWebpSources(page)).toHaveLength(0);
        expect(webpRequests).toEqual([]);
    });
});
