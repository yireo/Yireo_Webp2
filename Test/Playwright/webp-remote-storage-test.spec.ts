import {existsSync, readFileSync} from 'fs';
import {
    test,
    expect,
    configureWebp,
    gotoTestCase,
    expectWebpPicture,
} from '@yireo-webp2/webp';

/**
 * WebP images with Magento remote storage (AWS S3 or an S3-compatible service like MinIO), which requires the
 * remote storage support of Yireo_NextGenImages (https://github.com/yireo/Yireo_NextGenImages/pull/95)
 *
 * Requirements:
 * - Remote storage configured, e.g. `bin/magento setup:config:set --remote-storage-driver=aws-s3 ...`
 * - Media synchronized to the bucket with `bin/magento remote-storage:sync`
 * - Luma sample data (CMS images on the homepage)
 *
 * The tests are skipped when remote storage is not configured. Set REMOTE_STORAGE_DRIVER to override detection.
 */
const magentoRoot = process.env.MAGENTO_ROOT ?? '/tmp/magento';

function getRemoteStorageDriver(): string {
    if (process.env.REMOTE_STORAGE_DRIVER) {
        return process.env.REMOTE_STORAGE_DRIVER;
    }

    const envFile = magentoRoot + '/app/etc/env.php';
    if (!existsSync(envFile)) {
        return '';
    }

    const match = readFileSync(envFile, 'utf8').match(/'remote_storage'\s*=>\s*\[[^\]]*?'driver'\s*=>\s*'([^']+)'/s);
    return match ? match[1] : '';
}

const remoteStorageDriver = getRemoteStorageDriver();

test.describe('WebP images with remote storage', function () {
    test.skip(remoteStorageDriver === '' || remoteStorageDriver === 'file', 'Remote storage is not configured');

    test.beforeEach(async function ({page}) {
        await configureWebp(page);
    });

    test('converts CMS images that are stored on remote storage', async function ({page, pageErrors}) {
        await page.goto('/?nocache=' + Date.now());

        await expectWebpPicture(page, 'home-main');
        expect(pageErrors).toEqual([]);
    });

    test('serves the same WebP image on the next page view', async function ({page}) {
        await page.goto('/?nocache=' + Date.now());
        const firstSrcSet = await page.locator('picture:has(img[src*="/home-main."]) source[type="image/webp"]').getAttribute('srcset');

        await page.goto('/?nocache=' + Date.now());
        const secondSrcSet = await page.locator('picture:has(img[src*="/home-main."]) source[type="image/webp"]').getAttribute('srcset');

        expect(firstSrcSet).toBeTruthy();
        expect(secondSrcSet).toBe(firstSrcSet);
    });

    test('does not leave temporary copies behind', async function ({page}) {
        await page.goto('/?nocache=' + Date.now());
        await expectWebpPicture(page, 'home-main');

        const tmpFolder = magentoRoot + '/var/tmp/media/wysiwyg/home';
        expect(existsSync(tmpFolder + '/home-main.jpg'), 'Temporary copy of source image').toBe(false);
        expect(existsSync(tmpFolder + '/home-main.webp'), 'Temporary copy of WebP image').toBe(false);
    });

    /**
     * Static files are not stored on remote storage, so they should keep working as before
     */
    test('still converts static images that are stored locally', async function ({page}) {
        await gotoTestCase(page, 'multiple_images');

        await expectWebpPicture(page, 'flowers');
    });
});
