<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration;

use Magento\Framework\App\ObjectManager;
use Yireo\IntegrationTestHelper\Test\Integration\GraphQlTestCase;
use Yireo\NextGenImages\Image\Image;
use Yireo\Webp2\Convertor\Convertor;

class GraphQlTest extends GraphQlTestCase
{
    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple_with_media_gallery_entries.php
     * @magentoAppArea graphql
     * @magentoDbIsolation enabled
     * @magentoAppIsolation enabled
     * @magentoCache all disabled
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/enabled 1
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/debug 1
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/convert_images 1
     * @@magentoConfigFixture current_store yireo_nextgenimages/settings/target_directory same_as_source
     * @@magentoConfigFixture current_store yireo_webp2/settings/enabled 1
     * @@magentoConfigFixture current_store dev/static/sign 0
     * @return void
     */
    public function testGraphQlMediaGalleryQuery()
    {
        $data = $this->getGraphQlQueryData(file_get_contents(__DIR__ . '/fixtures/product_media_gallery.graphql'));

        $this->assertNotEmpty($data);
        $this->assertArrayHasKey('data', $data, var_export($data, true));
        $this->assertArrayHasKey('products', $data['data']);
        $this->assertArrayHasKey('items', $data['data']['products']);
        $this->assertNotEmpty($data['data']['products']['items']);

        $productData = $data['data']['products']['items'][0];
        $this->assertArrayHasKey('media_gallery', $productData);
        $this->assertNotEmpty($productData['media_gallery'], var_export($productData, true));

        $firstImageData = $productData['media_gallery'][0];
        $this->assertNotEmpty($firstImageData);
        $this->assertNotEmpty($firstImageData['url']);
        $this->assertArrayHasKey('url_webp', $firstImageData);
    }
}
