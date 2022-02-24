<?php declare(strict_types=1);

namespace Yireo\Webp2\Test\Utils;

use Yireo\Webp2\Convertor\ConvertWrapper;

/**
 * Class ConvertWrapper to wrap third party wrapper for purpose of preference overrides and testing
 */
class ConvertWrapperStub extends ConvertWrapper
{
    /**
     * @param string $sourceImageFilename
     * @param string $destinationImageFilename
     */
    public function convert(string $sourceImageFilename, string $destinationImageFilename): void
    {
        copy($sourceImageFilename, $destinationImageFilename);
    }
}
