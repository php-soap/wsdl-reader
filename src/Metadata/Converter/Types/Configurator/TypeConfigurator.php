<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class TypeConfigurator
{
    public function __invoke(MetaType $metaType, XsdType $xsdType, TypesConverterContext $context): MetaType
    {
        $restrictions = $xsdType->getRestriction();
        $parent = $xsdType->getParent();
        $extension = $xsdType->getExtension();
        $base = $restrictions?->getBase() ?: $extension?->getBase();

        return $metaType
            ->withMeta([
                ...$metaType->getMeta(),
                'docs' => $xsdType->getDoc(),
                'abstract' => $xsdType->isAbstract(),
                'restrictions' => $restrictions ? $restrictions->getChecks() : [],
                'parent' => $parent && method_exists($parent, 'getChecks') ? $parent->getChecks() : [],
                'extends' => $base?->getName() ?? '',
            ])
            ->withBaseType($base?->getName() ?? '');
    }
}
