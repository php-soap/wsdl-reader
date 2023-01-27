<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeContainer;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementContainer;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\WsdlReader\Metadata\Converter\Types\Rule\SkipArrayTypePropertiesRule;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;

final class PropertiesVisitor
{
    public function __invoke(Type $type, TypesConverterContext $context): PropertyCollection
    {
        return match (true) {
            (new SkipArrayTypePropertiesRule())($type, $context) => new PropertyCollection(),
            $type instanceof AttributeContainer && $type->getAttributes() => (new AttributeContainerVisitor())($type, $context),
            $type instanceof ElementContainer && $type->getElements() => (new ElementContainerVisitor())($type, $context),
            default => new PropertyCollection()
        };
    }
}
