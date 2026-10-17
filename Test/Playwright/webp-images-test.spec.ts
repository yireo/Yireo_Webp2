import {
    test,
    expect,
    configureWebp,
    gotoTestCase,
    getWebpSources,
    expectWebpPicture,
    hasWebpSource,
    TEST_IMAGES,
} from '@yireo-webp2/webp';

test.describe('WebP images', function () {
    test.beforeEach(async function ({page}) {
        await configureWebp(page);
    });

    test('wraps an image with an existing WebP version in a picture with a WebP source', async function ({page, pageErrors}) {
        await gotoTestCase(page, 'multiple_images');

        await expectWebpPicture(page, 'flowers');
        expect(pageErrors).toEqual([]);
    });

    test('only adds WebP sources for WebP images that exist', async function ({page}) {
        await gotoTestCase(page, 'multiple_images');

        const sources = await getWebpSources(page);
        expect(sources.length).toBeGreaterThan(0);
        for (const source of sources) {
            const webpUrl = source.trim().split(/\s+/)[0];
            const response = await page.request.get(webpUrl);
            expect(response.status(), `Status of ${webpUrl}`).toBe(200);
            expect(response.headers()['content-type']).toContain('image/webp');
        }

        await expect(page.locator('img[src*="/Yireo_Webp2/images/test/"]')).toHaveCount(TEST_IMAGES.length);
    });

    test('converts JPEG and PNG images to WebP', async function ({page}) {
        await gotoTestCase(page, 'multiple_images');

        const convertibleImages = TEST_IMAGES.filter(imageName => imageName !== 'flowers');
        for (const imageName of convertibleImages) {
            test.skip(!(await hasWebpSource(page, imageName)), 'No WebP convertor available on the server');
            await expectWebpPicture(page, imageName);
        }
    });

    test('uses the same WebP image for repeated images', async function ({page}) {
        await gotoTestCase(page, 'multiple_images_same');

        const sources = await getWebpSources(page);
        expect(sources).toHaveLength(3);
        expect(new Set(sources).size).toBe(1);
        await expectWebpPicture(page, 'flowers');
    });

    test('keeps the attributes of the original image', async function ({page}) {
        await gotoTestCase(page, 'image_with_custom_style');

        await expectWebpPicture(page, 'flowers');
        const image = page.locator('picture img.alt-img');
        await expect(image).toHaveAttribute('style', 'display:insane; opacity:666;');
        await expect(image).toHaveAttribute('alt', 'test sample');
    });

    test('leaves existing picture elements alone', async function ({page}) {
        await gotoTestCase(page, 'multiple_existing_picturesets');

        await expect(page.locator('picture')).toHaveCount(TEST_IMAGES.length);
        expect(await getWebpSources(page)).toHaveLength(0);
    });

    test('does not add a WebP source for external images', async function ({page, pageErrors}) {
        await page.route('https://example.com/**', route => route.abort());
        await gotoTestCase(page, 'unknown_image');

        await expect(page.locator('img[src="https://example.com/some/non/existing/image.png"]')).toBeAttached();
        expect(await getWebpSources(page)).toHaveLength(0);
        expect(pageErrors).toEqual([]);
    });
});
