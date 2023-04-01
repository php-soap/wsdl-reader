<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AbstractAttributeItem;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class AbstractAttributeItemConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof AbstractAttributeItem) {
            return $engineType;
        }

        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new RestrictionsConfigurator())($engineType, $xsdType->getType()?->getRestriction(), $context),
        );

        return $configure(
            $engineType
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withFixed($xsdType->getFixed())
                        ->withDefault($xsdType->getDefault())
                )
        );
    }
}
