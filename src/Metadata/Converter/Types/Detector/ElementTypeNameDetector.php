<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Element\Any\Any;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementRef;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\WsdlReader\Metadata\Converter\Types\ParentContext;

final class ElementTypeNameDetector
{
    public function __invoke(ElementItem $element, ParentContext $parentContext, ?string $calculatedTypeName = null): string
    {
        $type = $element instanceof ElementSingle ? $element->getType() : null;
        $typeName = $calculatedTypeName ?? ($type?->getName() ?: $element->getName());
        $rootParent = $parentContext->rootParent();

        // Add some conditions to validate if the element type name should be modified:
        if (
            $rootParent === $element // Dont enhance yourself
            || $element instanceof Any // Any types are just objects. 'any' is a proper name here.
            || $element instanceof ElementRef // Refs already have a proper name
            || $type?->getName() // Named types already have a proper name
        ) {
            return $typeName;
        }

        // Make sure the root parent has a name:
        if (
            !$rootParent instanceof Type
            && !$rootParent instanceof ElementItem
        ) {
            return $typeName;
        }

        $rootParentName = $rootParent->getName();
        if (!$rootParentName) {
            return $typeName;
        }

        return $rootParentName . ucfirst($typeName);
    }
}
