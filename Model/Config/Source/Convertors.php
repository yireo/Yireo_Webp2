<?php declare(strict_types=1);

namespace Yireo\Webp2\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Yireo\Webp2\Config\Config;

class Convertors implements OptionSourceInterface
{
    private const LABELS = [
        'cwebp' => 'cwebp binary',
        'gd' => 'GD PHP extension',
        'imagick' => 'Imagick PHP extension',
    ];

    public function toOptionArray()
    {
        $options = [];
        foreach (Config::CONVERTORS as $convertor) {
            $options[] = ['value' => $convertor, 'label' => self::LABELS[$convertor] ?? $convertor];
        }

        return $options;
    }
}
