<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class SimpleTypeConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof SimpleType) {
            return $engineType;
        }

        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new RestrictionsConfigurator())($engineType, $xsdType->getRestriction(), $context),
            static fn (EngineType $engineType): EngineType => (new SimpleBottomTypeConfigurator())($engineType, $xsdType, $context),
        );

        return $configure(
            $engineType
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsSimple(true)
                )
        );
    }
}
