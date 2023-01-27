<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class BindingOperationMessages
{
    /**
     * @var list<BindingOperation>
     */
    public readonly array $items;

    public function __construct(
        BindingOperation ... $items
    ) {
        $this->items = $items;
    }
}
