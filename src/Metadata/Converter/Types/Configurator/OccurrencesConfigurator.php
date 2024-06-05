<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\InterfaceSetMinMax;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class OccurrencesConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof InterfaceSetMinMax) {
            return $engineType;
        }

        $min = $xsdType->getMin();
        $max = $xsdType->getMax();
        $isNullable = $engineType->getMeta()->isNullable()->unwrapOr(false) || ($min === 0 && $max === 1);
        $isList = $engineType->getMeta()->isList()->unwrapOr(false) || ($max > 1 || $max === -1);

        return $engineType
            ->withBaseType($isList ? 'array' : $engineType->getBaseType())
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta
                    ->withMinOccurs($min)
                    ->withMaxOccurs($max)
                    ->withIsNullable($isNullable)
                    ->withIsList($isList)
                    ->withIsRepeatingElement($isList)
            );
    }
}
