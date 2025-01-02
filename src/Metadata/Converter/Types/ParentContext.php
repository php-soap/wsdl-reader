<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types;

use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use function Psl\Iter\first;
use function Psl\Iter\last;

final class ParentContext
{
    /**
     * @param non-empty-list<SchemaItem> $items
     */
    private function __construct(
        public readonly array $items,
    ) {
    }

    public static function create(SchemaItem $item): self
    {
        return new self([$item]);
    }

    public function withNextParent(SchemaItem $item): self
    {
        return new self([...$this->items, $item]);
    }

    public function rootParent(): SchemaItem
    {
        return first($this->items);
    }

    public function currentParent(): SchemaItem
    {
        return last($this->items);
    }
}
