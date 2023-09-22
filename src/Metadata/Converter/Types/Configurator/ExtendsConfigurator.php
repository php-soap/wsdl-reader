<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type as XsdType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class ExtendsConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof XsdType) {
            return $engineType;
        }

        $parent = $xsdType->getParent();
        $base = $parent?->getBase();
        $name = $base?->getName();
        if (!$name) {
            return $engineType;
        }

        return $engineType
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withExtends([
                    'type' => $name,
                    'namespace' => $base?->getSchema()->getTargetNamespace() ?? '',
                    'isSimple' => $base instanceof SimpleType,
                ])
            )
            ->withBaseType($name);
    }
}
