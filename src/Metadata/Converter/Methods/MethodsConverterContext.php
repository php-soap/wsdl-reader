<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods;

use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Locator\ServiceSelectionCriteria;

final class MethodsConverterContext
{
    private function __construct(
        public readonly TypeCollection $types,
        public readonly ServiceSelectionCriteria $serviceCriteria
    ) {
    }

    public static function defaults(TypeCollection $types, ?ServiceSelectionCriteria $serviceSelectionCriteria): self
    {
        return new self(
            $types,
            $serviceSelectionCriteria ?? ServiceSelectionCriteria::defaults()
        );
    }
}
