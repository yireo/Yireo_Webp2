import {test, expect, configureWebp, expectBodyClass, PRODUCT, gotoProduct} from '@yireo-webp2/webp';

test.describe('WebP gallery (Luma)', function () {
    test.beforeEach(async function ({page}) {
        await configureWebp(page, {product: PRODUCT});
    });

    test('registers the gallery and swatch mixins', async function ({page, pageErrors}) {
        await gotoProduct(page);
        await expectBodyClass(page, 'webp');

        const mixins = await page.evaluate(() => (window as any).require.s.contexts._.config.config.mixins);
        expect(mixins['mage/gallery/gallery']).toHaveProperty(['Yireo_Webp2/js/gallery-mixin'], true);
        expect(mixins['Magento_Swatches/js/swatch-renderer']).toHaveProperty(['Yireo_Webp2/js/swatch-renderer-mixin'], true);

        await expect(page.locator('[data-gallery-role="gallery-placeholder"]')).toBeAttached();
        expect(pageErrors).toEqual([]);
    });

    test('replaces gallery images with their WebP variants', async function ({page, pageErrors}) {
        await gotoProduct(page);

        const imagesData = await page.evaluate(() => new Promise(resolve => {
            (window as any).require(['Yireo_Webp2/js/gallery-mixin'], (galleryMixin: any) => {
                const mixin = galleryMixin({extend: (object: any) => object});
                const data = [
                    {full: 'a.jpg', full_webp: 'a.webp', img: 'b.jpg', img_webp: 'b.webp', thumb: 'c.jpg', thumb_webp: 'c.webp'},
                    {full: 'd.jpg', img: 'e.jpg', thumb: 'f.jpg'},
                ];
                mixin._replaceImageDataWithWebp(data);
                resolve(data);
            });
        }));

        expect(imagesData).toEqual([
            {full: 'a.webp', full_webp: 'a.webp', img: 'b.webp', img_webp: 'b.webp', thumb: 'c.webp', thumb_webp: 'c.webp'},
            {full: 'd.jpg', img: 'e.jpg', thumb: 'f.jpg'},
        ]);
        expect(pageErrors).toEqual([]);
    });
});
