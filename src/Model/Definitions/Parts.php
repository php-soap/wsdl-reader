<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class Parts
{
    /**
     * @var list<Part>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        Part ... $items
    ) {
        $this->items = $items;
    }
}
