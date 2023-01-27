<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Inheritance\Restriction;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class TypeConfigurator
{
    public function __invoke(MetaType $metaType, XsdType $xsdType, TypesConverterContext $context): MetaType
    {
        $parent = $xsdType->getParent();
        $base = $parent?->getBase();
        $checks = $parent instanceof Restriction ? $parent->getChecks() : [];

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'docs' => $xsdType->getDoc(),
                'abstract' => $xsdType->isAbstract(),
                'restrictions' => $checks,
                'extends' => $base?->getName() ?? '',
                'extendsNamespace' => $base?->getSchema()->getTargetNamespace() ?? '',
            ])
            ->withBaseType($base?->getName() ?? '');
    }
}
