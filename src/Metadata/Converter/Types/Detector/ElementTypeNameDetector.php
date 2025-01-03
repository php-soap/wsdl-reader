<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\WsdlReader\Metadata\Converter\Types\ParentContext;

final class ElementTypeNameDetector
{
    public function __invoke(ElementItem $element, ParentContext $parentContext, ?string $calculatedTypeName = null): string
    {
        $type = $element instanceof ElementSingle ? $element->getType() : null;
        $typeName = $calculatedTypeName ?? ($type?->getName() ?: $element->getName());

        // For inline simple types, we prefix the name of the element with the name of the parent type.
        if ($type instanceof SimpleType && !$type->getName()) {
            $parent = $parentContext->currentParent();

            if ($parent instanceof Type || $parent instanceof ElementItem) {
                if ($parentName = $parent->getName()) {
                    $typeName = $parentName . ucfirst($typeName);
                }
            }
        }

        return $typeName;
    }
}
