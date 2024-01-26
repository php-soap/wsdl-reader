<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class AttributeConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof AttributeItem) {
            return $engineType;
        }

        return pipe(
            static fn (EngineType $engineType): EngineType => (new XmlTypeInfoConfigurator())($engineType, $xsdType, $context),
            static fn (EngineType $engineType): EngineType => (new DocsConfigurator())($engineType, $xsdType, $context),
            static fn (EngineType $engineType): EngineType => (new AttributeSingleConfigurator())($engineType, $xsdType, $context),
            static fn (EngineType $engineType): EngineType => (new AbstractAttributeItemConfigurator())($engineType, $xsdType, $context),
            static fn (EngineType $engineType): EngineType => (new AttributeBaseTypeConfigurator())($engineType, $xsdType, $context),
        )(
            $engineType
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta->withIsAttribute(true)
                )
        );
    }
}
