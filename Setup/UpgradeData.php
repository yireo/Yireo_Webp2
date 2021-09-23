<?php declare(strict_types=1);

namespace Yireo\Webp2\Setup;

use Magento\Config\Model\ResourceModel\Config\Data\CollectionFactory;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var CollectionFactory
     */
    private $configCollectionFactory;

    /**
     * @var WriterInterface
     */
    private $storageWriter;

    /**
     * UpgradeData constructor.
     * @param CollectionFactory $configCollectionFactory
     * @param WriterInterface $storageWriter
     */
    public function __construct(
        CollectionFactory $configCollectionFactory,
        WriterInterface $storageWriter
    ) {

        $this->configCollectionFactory = $configCollectionFactory;
        $this->storageWriter = $storageWriter;
    }

    /**
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     * @return void
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $oldPath = 'system/yireo_webp/';
        $newPath = 'yireo_webp2/settings/';

        $configCollection = $this->configCollectionFactory->create();
        $configCollection->addFieldToFilter('path', ['like' => $oldPath . '%']);
        $items = $configCollection->getItems();

        foreach ($items as $item) {
            $data = $item->getData();
            $this->storageWriter->delete($data['path'], $data['scope'], $data['scope_id']);
            $newPath = str_replace($oldPath, $newPath, $data['path']);
            $this->storageWriter->save($newPath, $data['value'], $data['scope'], $data['scope_id']);
        }

        $setup->endSetup();
    }
}
