<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class ExtendsConfigurator
{
    public function __invoke(MetaType $metaType, mixed $xsdType, TypesConverterContext $context): MetaType
    {
        if (!$xsdType instanceof XsdType) {
            return $metaType;
        }

        $parent = $xsdType->getParent();
        $base = $parent?->getBase();
        $name = $base?->getName();
        if (!$name) {
            return $metaType;
        }

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'extends' => $name,
                'extendsNamespace' => $base?->getSchema()->getTargetNamespace() ?? '',
            ])
            ->withBaseType($name);
    }
}
