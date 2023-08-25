<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\AbstractElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementRef;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Vec\flat_map;

/**
 * Both complex types and elements can contain inline elements through complex types.
 * This visitor unwraps them and places them in their own standalone types.
 */
final class InlineElementTypeVisitor
{
    public function __invoke(Type $xsdType, TypesConverterContext $context): TypeCollection
    {
        if (!$xsdType instanceof ComplexType) {
            return new TypeCollection();
        }

        $elementVisitor = new ElementVisitor();

        return new TypeCollection(
            ...flat_map(
                $xsdType->getElements(),
                static function (ElementItem $element) use ($elementVisitor, $context): TypeCollection {
                    if (!$element instanceof AbstractElementSingle || $element instanceof ElementRef) {
                        return new TypeCollection();
                    }

                    // There is no need to create types for simple elements like strings.
                    if (!$element->getType() instanceof ComplexType || !$element->isLocal()) {
                        return new TypeCollection();
                    }

                    // If the element links to a named type, we already know about it.
                    if ($element->getType()?->getName()) {
                        return new TypeCollection();
                    }

                    return $elementVisitor($element, $context);
                }
            )
        );
    }
}
