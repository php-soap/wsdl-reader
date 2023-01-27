<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeSingle;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class AttributeSingleConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof AttributeSingle) {
            return $metaType;
        }

        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new RestrictionsConfigurator())($metaType, $xsdType->getType()?->getRestriction(), $context),
        );

        return $configure(
            $metaType
                ->withMeta([
                    ...$metaType->getMeta(),
                    'qualified' => $xsdType->isQualified(),
                    'nil' => $xsdType->isNil(),
                    'isNullable' => ((bool) ($metaType->getMeta()['isNullable'] ?? false)) || $xsdType->isNil(),
                    'use' => $xsdType->getUse(),
                ])
        );
    }
}
