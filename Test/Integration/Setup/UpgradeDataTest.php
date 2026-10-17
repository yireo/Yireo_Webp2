<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Integration\Setup;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use Yireo\Webp2\Setup\UpgradeData;

class UpgradeDataTest extends TestCase
{
    /**
     * @magentoDbIsolation enabled
     */
    public function testIfOldConfigPathsAreMoved()
    {
        $objectManager = Bootstrap::getObjectManager();
        $writer = $objectManager->get(WriterInterface::class);
        $writer->save('system/yireo_webp/quality_level', '42');
        $writer->save('system/yireo_webp/encoding', 'lossless', 'stores', 1);

        $upgradeData = $objectManager->create(UpgradeData::class);
        $upgradeData->upgrade(
            $objectManager->get(ModuleDataSetupInterface::class),
            $this->createMock(ModuleContextInterface::class)
        );

        $this->assertSame([], $this->getConfigValues('system/yireo_webp/%'));
        $this->assertSame([
            'stores/1/yireo_webp2/settings/encoding' => 'lossless',
            'default/0/yireo_webp2/settings/quality_level' => '42',
        ], $this->getConfigValues('yireo_webp2/settings/%'));
    }

    /**
     * @param string $pathFilter
     * @return string[]
     */
    private function getConfigValues(string $pathFilter): array
    {
        $collection = Bootstrap::getObjectManager()->get(CollectionFactory::class)->create();
        $collection->addFieldToFilter('path', ['like' => $pathFilter]);
        $collection->setOrder('path', 'ASC');

        $values = [];
        foreach ($collection->getItems() as $item) {
            $key = $item->getData('scope') . '/' . $item->getData('scope_id') . '/' . $item->getData('path');
            $values[$key] = (string)$item->getData('value');
        }

        return $values;
    }
}
