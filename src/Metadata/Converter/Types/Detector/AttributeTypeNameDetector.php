<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeSingle;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Psl\Option\Option;
use Soap\WsdlReader\Metadata\Converter\Types\ParentContext;
use function Psl\Option\from_nullable;

final class AttributeTypeNameDetector
{
    public function __invoke(AttributeItem $attribute, ParentContext $parentContext): string
    {
        $attributeType = $attribute instanceof AttributeSingle ? $attribute->getType() : null;
        $attributeRestriction = $attributeType?->getRestriction();
        $attributeTypeName = $attributeType?->getName();
        $attributeRestrictionName = ($attributeRestriction && !$attributeRestriction->getChecks()) ? $attributeRestriction->getBase()?->getName() : null;

        $typeName = $attributeTypeName ?: ($attributeRestrictionName ?: $attribute->getName());

        // If a name cannot be determined from the type, we fallback to the attribute name:
        // Prefix the attribute name with the parent element name resulting in a more unique type-name.
        if (!$attributeTypeName && !$attributeRestrictionName) {
            $typeName = (new AttributeDeclaringParentTypeDetector())($attribute, $parentContext->currentParent())
                ->andThen(static fn (Type $parent): Option => from_nullable($parent->getName()))
                ->map(static fn (string $parentName): string => $parentName . ucfirst($typeName))
                ->unwrapOr($typeName);
        }

        return $typeName;
    }
}
