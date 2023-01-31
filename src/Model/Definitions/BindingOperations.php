<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class BindingOperations
{
    /**
     * @var list<BindingOperation>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        BindingOperation ... $items
    ) {
        $this->items = $items;
    }
}
