<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeSingle;
use GoetasWebservices\XML\XSDReader\Schema\CustomAttribute;
use Psl\Option\Option;
use function Psl\Iter\search;
use function Psl\Option\from_nullable;
use function Psl\Option\none;

final class AttributesCustomAttributeDetector
{
    /**
     * Detect custom attributes for a given attribute inside a dictionary of attributes grouped by name.
     *
     * @param array<string, AttributeItem> $attributes
     * @return Option<CustomAttribute>
     */
    public function __invoke(array $attributes, string $attributeName, string $metadataName): Option
    {
        $attribute = $attributes[$attributeName] ?? null;
        if (!$attribute instanceof AttributeSingle) {
            return none();
        }

        $meta = search(
            $attribute->getCustomAttributes(),
            static fn (CustomAttribute $meta): bool => $meta->getName() === $metadataName
        );

        return from_nullable($meta);
    }
}
