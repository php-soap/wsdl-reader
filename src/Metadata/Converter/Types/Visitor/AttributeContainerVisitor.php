<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeContainer;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\Group;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use RuntimeException;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;
use function Psl\Iter\first;
use function Psl\Result\wrap;
use function Psl\Type\instance_of;
use function Psl\Vec\flat_map;

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
        $element = wrap(static function () use ($container, $context) : Property {
            $type = instance_of(Type::class)->assert($container);
            $properties = (new ComplexBaseTypeVisitor())($type, $context);

            if ($properties->count() !== 1) {
                throw new RuntimeException('Expected only 1 element to be available as the attribute containers base.');
            }

            return first($properties);
        });

        return $element->proceed(
            static fn (Property $detected): PropertyCollection => new PropertyCollection(
                new Property('_', $detected->getType())
            ),
            static fn (): PropertyCollection => new PropertyCollection(),
        );
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

        // TODO ! Better type exists validation
        $attributeType = method_exists($attribute, 'getType') ? $attribute->getType() : null;
        $typeName = $attributeType?->getName() ?: $attribute->getName();

        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new Configurator\AttributeConfigurator())($metaType, $attribute, $context),
        );

        return new PropertyCollection(
            new Property(
                $attribute->getName(),
                $configure(MetaType::guess($typeName))
            )
        );
    }
}
