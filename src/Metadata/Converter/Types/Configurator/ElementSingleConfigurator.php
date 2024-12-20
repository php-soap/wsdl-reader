<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class ElementSingleConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof ElementSingle) {
            return $engineType;
        }

        // Elements can have inline types. Mark the attribute as a local type in that case.
        $innerType = $xsdType->getType();
        $isConsideredAnInlineType = $innerType && $innerType->getName() === null;
        if ($isConsideredAnInlineType) {
            $engineType = $engineType->withMeta(
                static fn ($meta) => $meta->withIsLocal(true)
            );
        }

        return $engineType
            ->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta
                    ->withIsQualified($xsdType->isQualified())
                    ->withIsNil($xsdType->isNil())
                    ->withIsNullable($meta->isNullable()->unwrapOr(false) || $xsdType->isNil())
            );
    }
}
