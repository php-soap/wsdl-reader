<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class ElementSingleConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof ElementSingle) {
            return $metaType;
        }

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'qualified' => $xsdType->isQualified(),
                'local' => $xsdType->isLocal(),
                'nil' => $xsdType->isNil(),
                'isNullable' => ((bool) ($metaType->getMeta()['isNullable'] ?? false)) || $xsdType->isNil(),
            ]);
    }
}
