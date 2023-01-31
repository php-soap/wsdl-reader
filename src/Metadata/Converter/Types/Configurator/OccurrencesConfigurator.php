<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\InterfaceSetMinMax;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class OccurrencesConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof InterfaceSetMinMax) {
            return $metaType;
        }

        $min = $xsdType->getMin();
        $max = $xsdType->getMax();
        $isNullable = ((bool) ($metaType->getMeta()['isNullable'] ?? false)) || ($min === 0 && $max === 1);
        $isList = ((bool) ($metaType->getMeta()['isList'] ?? false)) || ($max > 1 || $max === -1);

        return $metaType
            ->withBaseType($isList ? 'array' : $metaType->getBaseType())
            ->withMeta([
                ...$metaType->getMeta(),
                'min' => $min,
                'max' => $max,
                'isNullable' => $isNullable,
                'isList' => $isList,
            ]);
    }
}
