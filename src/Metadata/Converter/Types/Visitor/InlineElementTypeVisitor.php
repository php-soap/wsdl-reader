<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\AbstractElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementContainer;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementRef;
use GoetasWebservices\XML\XSDReader\Schema\Type\BaseComplexType;
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

        return new TypeCollection(
            ...flat_map(
                $xsdType->getElements(),
                fn (ElementItem $element): TypeCollection => $this->detectInlineTypes($element, $context)
            )
        );
    }

    private function detectInlineTypes(ElementItem $element, TypesConverterContext $context): TypeCollection
    {
        $elementVisitor = new ElementVisitor();

        // Handle nested element containers (like "choice" with inline elements)
        if ($element instanceof ElementContainer) {
            return new TypeCollection(
                ...flat_map(
                    $element->getElements(),
                    fn (ElementItem $child): TypeCollection => $this->detectInlineTypes($child, $context)
                )
            );
        }

        if (!$element instanceof AbstractElementSingle || $element instanceof ElementRef) {
            return new TypeCollection();
        }

        // There is no need to create types for simple elements like strings.
        if (!$element->getType() instanceof BaseComplexType || !$element->isLocal()) {
            return new TypeCollection();
        }

        // If the element links to a named type, we already know about it.
        if ($element->getType()?->getName()) {
            return new TypeCollection();
        }

        return $elementVisitor($element, $context);
    }
}
