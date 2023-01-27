<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class SimpleTypeConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof SimpleType) {
            return $metaType;
        }

        // TODO : Parse lists and unions

        return $metaType->withMeta([
            ...$metaType->getMeta(),
            'list' => ['TODO'],
            'union' => ['TODO'],
        ]);
    }
}
