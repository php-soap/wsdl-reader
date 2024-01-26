<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class ElementConfigurator
{
    public function __invoke(EngineType $engineType, mixed $element, TypesConverterContext $context): EngineType
    {
        if (!$element instanceof ElementSingle) {
            return $engineType;
        }

        $xsdType = $element->getType();

        return pipe(
            static fn (EngineType $engineType): EngineType => $engineType->withMeta(
                static fn (TypeMeta $meta): TypeMeta => $meta->withIsElement(true)
            ),
            static fn (EngineType $engineType): EngineType => (new TypeConfigurator())($engineType, $xsdType, $context),
            static fn (EngineType $engineType): EngineType => (new XmlTypeInfoConfigurator())($engineType, $element, $context),
            static fn (EngineType $engineType): EngineType => (new DocsConfigurator())($engineType, $element, $context),
            static fn (EngineType $engineType): EngineType => (new DefaultConfigurator())($engineType, $element, $context),
            static fn (EngineType $engineType): EngineType => (new FixedConfigurator())($engineType, $element, $context),
            static fn (EngineType $engineType): EngineType => (new OccurrencesConfigurator())($engineType, $element, $context),
            static fn (EngineType $engineType): EngineType => (new ElementSingleConfigurator())($engineType, $element, $context),
        )($engineType);
    }
}
