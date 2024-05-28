<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeContainer;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use function Psl\Dict\reindex;
use function Psl\Iter\reduce;
use function Psl\Vec\flat_map;

final class NamedAttributesDetector
{
    /**
     * @return array<string, AttributeItem>
     */
    public function __invoke(Type $type): array
    {
        if (!$type instanceof AttributeContainer) {
            return [];
        }

        return reindex(
            flat_map(
                $type->getAttributes(),
                $this->flattenContainer(...)
            ),
            static fn (AttributeItem $item): string => $item->getName()
        );
    }

    /**
     * @return list<AttributeItem>
     */
    private function flattenContainer(AttributeItem $current): array
    {
        if (!$current instanceof AttributeContainer) {
            return [$current];
        }

        return reduce(
            $current->getAttributes(),
            /**
             * @param list<AttributeItem> $carry
             * @param AttributeItem $attribute
             *
             * @return list<AttributeItem>
             */
            fn (array $carry, AttributeItem $attribute) => [
                ...$carry,
                ...$this->flattenContainer($attribute)
            ],
            []
        );
    }
}
