import {test as baseTest, expect, Page} from '@playwright/test';

export const HYVA_THEME = 'Hyva/default-csp';

export const PRODUCT_SKU = 'yireo-webp2-product';

/**
 * A dedicated SKU makes the configure endpoint create this product, instead of reusing any existing product
 */
export const PRODUCT = {sku: PRODUCT_SKU, name: 'Yireo WebP2 Product'};

export const PRODUCT_URL = '/' + PRODUCT_SKU + '.html';

export const TEST_IMAGES = ['flowers', 'goku', 'transparent-dragon'];

export const defaultConfig = {
    'yireo_webp2/settings/enabled': 1,
    'yireo_webp2/settings/encoding': 'lossy',
    'yireo_webp2/settings/quality_level': 80,
    'yireo_nextgenimages/settings/enabled': 1,
    'yireo_nextgenimages/settings/convert_images': 1,
    'yireo_nextgenimages/settings/target_directory': 'same_as_source',
    'yireo_nextgenimages/settings/hash': 0,
    'dev/static/sign': 0,
};

const token = () => process.env.TEST_TOKEN;

/**
 * Payload accepted by the Loki_FunctionalTests configure endpoint
 */
export type ConfigurePayload = {
    config?: Record<string, any>;
    product?: boolean | Record<string, any>;
    [key: string]: any;
};

/**
 * Configure the store via the Loki_FunctionalTests endpoint, merging `defaultConfig` underneath `config`
 */
export async function configureWebp(page: Page, payload: ConfigurePayload = {}) {
    const requestData = {
        ...payload,
        config: {...defaultConfig, ...(payload.config ?? {})},
    };

    const response = await page.request.post('/loki_functional_tests/index/configure?token=' + token(), {
        form: {
            config: JSON.stringify(requestData),
        },
    });

    const body = await response.text();
    let data: any;
    try {
        data = JSON.parse(body);
    } catch (error) {
        throw new Error(`Configure endpoint returned no JSON (status ${response.status()}): ${body.slice(0, 500)}`);
    }

    if (data.error) {
        throw new Error('Configure error: ' + data.error);
    }

    const themeErrors = data.result?.theme?.errors ?? [];
    if (themeErrors.length > 0) {
        throw new Error('Configure theme error: ' + themeErrors.join(', '));
    }
}

/**
 * Configure the store with Hyva as frontend theme
 *
 * The endpoint falls back to Magento/luma whenever `theme` is missing, so every configure call in a
 * Hyva test must go through this function.
 */
export async function configureHyvaWebp(page: Page, payload: ConfigurePayload = {}) {
    try {
        await configureWebp(page, {...payload, theme: HYVA_THEME});
    } catch (error) {
        const message = error instanceof Error ? error.message : String(error);
        baseTest.skip(message.includes(`Theme "${HYVA_THEME}" not found`), `Hyvä theme "${HYVA_THEME}" is not installed`);
        throw error;
    }
}

export async function gotoProduct(page: Page) {
    const response = await page.goto(PRODUCT_URL);
    expect(response?.status(), 'Status of product page ' + PRODUCT_URL).toBe(200);
}

/**
 * Open a test case of the controller Yireo\Webp2\Controller\Test\Images
 *
 * In developer mode, static files are only published once they are requested, while WebP images can only be
 * generated for (or detected as) existing files. So the page is opened once, all images and their WebP counterparts
 * (like the shipped flowers.webp) are requested and then the page is opened again. A unique parameter bypasses the
 * Full Page Cache.
 */
export async function gotoTestCase(page: Page, testCase: string) {
    const url = (): string => '/webp/test/images?case=' + testCase + '&nocache=' + Date.now() + Math.random();

    await page.goto(url());
    const imageUrls = await page.locator('img').evaluateAll(
        (images: HTMLImageElement[]) => images.map(image => image.src)
    );

    for (const imageUrl of imageUrls) {
        if (imageUrl.startsWith(new URL(page.url()).origin)) {
            await page.request.get(imageUrl);
            await page.request.get(imageUrl.replace(/\.(jpg|jpeg|png)$/, '.webp'));
        }
    }

    await page.goto(url());
}

/**
 * Return whether the server was able to generate a WebP image for the given test image
 *
 * Images shipped with a WebP version (flowers) always work, but converting other images requires a working convertor
 * (cwebp, GD or Imagick with WebP support). Without one, no WebP source is added at all.
 */
export async function hasWebpSource(page: Page, imageName: string): Promise<boolean> {
    return await page.locator(`picture:has(img[src*="/${imageName}."]) source[type="image/webp"]`).count() > 0;
}

export async function getWebpSources(page: Page): Promise<string[]> {
    return page.locator('picture source[type="image/webp"]').evaluateAll(
        (sources: HTMLSourceElement[]) => sources.map(source => source.srcset)
    );
}

/**
 * Assert that the image is wrapped in a picture element with a WebP source that is actually served as WebP
 */
export async function expectWebpPicture(page: Page, imageName: string) {
    const image = page.locator(`picture img[src*="/${imageName}."]`).first();
    await expect(image, `Image "${imageName}" inside picture`).toBeAttached();

    const picture = image.locator('xpath=ancestor::picture[1]');
    const source = picture.locator('source[type="image/webp"]');
    await expect(source, `WebP source of "${imageName}"`).toHaveCount(1);

    const srcset = (await source.getAttribute('srcset')) ?? '';
    const webpUrl = srcset.trim().split(/\s+/)[0];
    expect(webpUrl).toMatch(new RegExp(`/${imageName}\\.webp$`));

    const response = await page.request.get(webpUrl);
    expect(response.status(), `Status of ${webpUrl}`).toBe(200);
    expect(response.headers()['content-type']).toContain('image/webp');

    await expect.poll(
        () => image.evaluate((element: HTMLImageElement) => element.complete ? element.currentSrc : ''),
        {message: `Browser picks the WebP image for "${imageName}"`}
    ).toMatch(/\.webp$/);
}

/**
 * Make the canvas-based WebP detection fail, like in a browser without WebP support
 */
export async function disableWebpSupport(page: Page) {
    await page.addInitScript(() => {
        HTMLCanvasElement.prototype.toDataURL = function () {
            return 'data:image/png;base64,';
        };
    });
}

/**
 * Open the URL and return the nonces that are allowed by its CSP headers
 */
export async function gotoAndGetCspNonces(page: Page, url: string): Promise<string[]> {
    const response = await page.goto(url);
    expect(response, 'Response of ' + url).not.toBeNull();
    const headers = await response!.allHeaders();
    const policy = [
        headers['content-security-policy'] ?? '',
        headers['content-security-policy-report-only'] ?? '',
    ].join(';');

    return [...policy.matchAll(/'nonce-([^']+)'/g)].map(match => match[1]);
}

export async function expectBodyClass(page: Page, className: string) {
    await expect(page.locator('body')).toHaveClass(new RegExp(`(^|\\s)${className}(\\s|$)`));
}

export const test = baseTest.extend<{ pageErrors: string[] }>({
    pageErrors: async ({page}, use) => {
        const pageErrors: string[] = [];
        page.on('pageerror', error => pageErrors.push(error.message));
        page.on('console', message => {
            if (message.type() === 'error' && message.text().includes('Yireo_WebP2')) {
                pageErrors.push(message.text());
            }
        });
        await use(pageErrors);
    },
});

export {expect} from '@playwright/test';
