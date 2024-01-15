<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeContainer;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeSingle;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\Group;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;
use function Psl\Result\wrap;
use function Psl\Type\instance_of;
use function Psl\Vec\flat_map;
use function Psl\Vec\map;

final class AttributeContainerVisitor
{
    public function __invoke(AttributeContainer $container, TypesConverterContext $context): PropertyCollection
    {
        return new PropertyCollection(
            ...$this->parseElementType($container, $context),
            ...$this->parseAttributes($container, $context)
        );
    }

    private function parseElementType(AttributeContainer $container, TypesConverterContext $context): PropertyCollection
    {
        $elements = wrap(static function () use ($container, $context) : PropertyCollection {
            $type = instance_of(Type::class)->assert($container);

            return new PropertyCollection(
                ...map(
                    (new ComplexBaseTypeVisitor())($type, $context),
                    static fn (Property $property): Property => new Property(
                        $property->getType()->getMeta()->isElement()->mapOrElse(
                            static fn (): string => $property->getName(),
                            static fn (): string => '_'
                        )->unwrap(),
                        $property->getType()->withMeta(
                            static fn (TypeMeta $meta): TypeMeta => $meta->withIsElementValue(true)
                        )
                    )
                )
            );
        });

        return $elements->catch(static fn () => new PropertyCollection())->getResult();
    }

    private function parseAttributes(AttributeContainer $container, TypesConverterContext $context): PropertyCollection
    {
        return new PropertyCollection(
            ...$this->parseExtendedAttributes($container, $context),
            ...flat_map(
                $container->getAttributes(),
                fn (AttributeItem $attribute) => $this->parseAttribute($attribute, $context)
            )
        );
    }

    /**
     * Make sure to parse attributes on extended / restricted complex types as well!
     */
    private function parseExtendedAttributes(AttributeContainer $container, TypesConverterContext $context): PropertyCollection
    {
        if (!$container instanceof Type) {
            return new PropertyCollection();
        }

        $base = $container->getParent()?->getBase();
        if (!$base instanceof AttributeContainer) {
            return new PropertyCollection();
        }

        return $this->parseAttributes($base, $context);
    }

    private function parseAttribute(AttributeItem $attribute, TypesConverterContext $context): PropertyCollection
    {
        if ($attribute instanceof Group) {
            return $this->parseAttributes($attribute, $context);
        }

        $attributeType = $attribute instanceof AttributeSingle ? $attribute->getType() : null;
        $typeName = $attributeType?->getName() ?: $attribute->getName();

        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new Configurator\AttributeConfigurator())($engineType, $attribute, $context),
        );

        return new PropertyCollection(
            new Property(
                $attribute->getName(),
                $configure(EngineType::guess($typeName))
            )
        );
    }
}
