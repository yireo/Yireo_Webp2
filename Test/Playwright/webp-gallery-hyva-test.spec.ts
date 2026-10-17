import {test, expect, configureHyvaWebp, PRODUCT, gotoProduct} from '@yireo-webp2/webp';

/**
 * Hyva variant of webp-gallery-test.spec.ts: With Hyva, the gallery data is changed via the events
 * dispatched by the Hyva gallery and configurable options
 */

test.describe('WebP gallery (Hyva)', function () {
    test.beforeEach(async function ({page}) {
        await configureHyvaWebp(page, {product: PRODUCT});
        await gotoProduct(page);
        expect(await page.evaluate(() => typeof (window as any).hasWebP)).toBe('function');
    });

    test('replaces gallery images with their WebP variants on "gallery-init"', async function ({page, pageErrors}) {
        const galleryData = await page.evaluate(() => {
            const detail = {
                images: [{full: 'a.jpg', full_webp: 'a.webp', img: 'b.jpg', img_webp: 'b.webp', thumb: 'c.jpg', thumb_webp: 'c.webp'}],
                initialImages: [{full: 'd.jpg', full_webp: 'd.webp', img: 'e.jpg', thumb: 'f.jpg'}],
            };
            document.dispatchEvent(new CustomEvent('gallery-init', {detail}));
            return detail;
        });

        expect(galleryData).toEqual({
            images: [{full: 'a.webp', img: 'b.webp', thumb: 'c.webp'}],
            initialImages: [{full: 'd.webp', img: 'e.jpg', thumb: 'f.jpg'}],
        });
        expect(pageErrors).toEqual([]);
    });

    test('replaces configurable images with their WebP variants on "configurable-options-init"', async function ({page, pageErrors}) {
        const optionConfig = await page.evaluate(() => {
            const detail = {
                images: {
                    '42': [{full: 'a.jpg', full_webp: 'a.webp', img: 'b.jpg', img_webp: 'b.webp', thumb: 'c.jpg'}],
                },
            };
            document.dispatchEvent(new CustomEvent('configurable-options-init', {detail}));
            return detail;
        });

        expect(optionConfig).toEqual({
            images: {
                '42': [{full: 'a.webp', img: 'b.webp', thumb: 'c.jpg'}],
            },
        });
        expect(pageErrors).toEqual([]);
    });

    test('updates the WebP source on "after-product-item-image-update"', async function ({page, pageErrors}) {
        const srcset = await page.evaluate(() => {
            const picture = document.createElement('picture');
            picture.innerHTML = '<source type="image/webp" srcset="/media/old.webp"><img src="/media/new.jpg" alt="">';
            document.body.appendChild(picture);

            const image = picture.querySelector('img')!;
            document.dispatchEvent(new CustomEvent('after-product-item-image-update', {detail: image}));
            return picture.querySelector('source')!.srcset;
        });

        expect(srcset).toMatch(/\/media\/new\.webp$/);
        expect(pageErrors).toEqual([]);
    });
});
