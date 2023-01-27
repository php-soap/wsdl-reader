<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class BindingOperationMessages
{
    /**
     * @var list<BindingOperationMessage>
     */
    public readonly array $items;

    public function __construct(
        BindingOperationMessage ... $items
    ) {
        $this->items = $items;
    }
}
