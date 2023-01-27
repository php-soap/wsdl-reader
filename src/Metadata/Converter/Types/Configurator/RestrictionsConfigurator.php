<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Inheritance\Restriction;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class RestrictionsConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof Restriction) {
            return $metaType;
        }

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'restriction' => $xsdType->getChecks(),
            ]);
    }
}
