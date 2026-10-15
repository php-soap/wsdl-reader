<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Configurator;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeRef;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeSingle;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class AttributeSingleConfigurator
{
    public function __invoke(EngineType $engineType, mixed $xsdType, TypesConverterContext $context): EngineType
    {
        if (!$xsdType instanceof AttributeSingle) {
            return $engineType;
        }

        // A ref points to a global attribute declaration, which is always qualified with the target namespace
        // of the schema that declares it. The form and attributeFormDefault rules only apply to local declarations.
        $isQualified = $xsdType instanceof AttributeRef || $xsdType->isQualified();

        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new RestrictionsConfigurator())($engineType, $xsdType->getType()?->getRestriction(), $context),
        );

        return $configure(
            $engineType
                ->withMeta(
                    static fn (TypeMeta $meta): TypeMeta => $meta
                        ->withIsQualified($isQualified)
                        ->withIsNil($xsdType->isNil())
                        ->withIsNullable($meta->isNullable()->unwrapOr(false) || $xsdType->isNil())
                        ->withUse($xsdType->getUse())
                )
        );
    }
}
