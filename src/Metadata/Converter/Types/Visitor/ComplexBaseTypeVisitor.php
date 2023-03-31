<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexType;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexTypeSimpleContent;
use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;
use function Psl\Type\instance_of;

final class ComplexBaseTypeVisitor
{
    public function __invoke(Type $type, TypesConverterContext $context): PropertyCollection
    {
        return match (true) {
            $type instanceof SimpleType => $this->parseSimpleContent($type, $context),
            $type instanceof ComplexTypeSimpleContent => $this->parseComplexTypeSimpleContent($type, $context),
            $type instanceof ComplexType => $this->parseComplexContent($type, $context),
            default => new PropertyCollection()
        };
    }

    private function parseSimpleContent(SimpleType $type, TypesConverterContext $context): PropertyCollection
    {
        $baseType = instance_of(Type::class)->assert($type->getParent()?->getBase());
        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new Configurator\TypeConfigurator())($engineType, $baseType, $context),
        );
        $typeName = $type->getName() ?? '';

        return new PropertyCollection(
            new Property(
                $typeName,
                $configure(EngineType::guess($baseType->getName() ?: $typeName))
            )
        );
    }

    private function parseComplexTypeSimpleContent(ComplexTypeSimpleContent $type, TypesConverterContext $context): PropertyCollection
    {
        $baseType = instance_of(Type::class)->assert($type->getParent()?->getBase());
        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new Configurator\TypeConfigurator())($engineType, $baseType, $context),
        );

        // Nested complexTypes with simple content...
        if ($baseType instanceof ComplexTypeSimpleContent) {
            return $this->parseComplexTypeSimpleContent($baseType, $context);
        }

        return new PropertyCollection(
            new Property(
                $baseType->getName() ?? '',
                $configure(EngineType::guess($baseType->getName() ?: ($type->getName() ?? '')))
            )
        );
    }

    private function parseComplexContent(ComplexType $type, TypesConverterContext $context): PropertyCollection
    {
        // The base type can refer to other complex / simple types.
        $baseType = $type->getParent()?->getBase();

        return new PropertyCollection(...[
            ...($baseType ? $this->__invoke($baseType, $context) : []),
            ...($type->getElements() ? (new ElementContainerVisitor())($type, $context) : [])
        ]);
    }
}
