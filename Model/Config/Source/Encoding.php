<?php declare(strict_types=1);

namespace Yireo\Webp2\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class Encoding implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            ['value' => 'lossy', 'label' => 'Lossy'],
            ['value' => 'lossless', 'label' => 'Lossless'],
            ['value' => 'auto', 'label' => 'Auto (both lossy and lossless)'],
        ];
    }
}
