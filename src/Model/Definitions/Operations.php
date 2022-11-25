<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Operations
{
    /**
     * @var list<Operation>
     */
    public readonly array $items;

    public function __construct(
        Operation ... $items
    ){
        $this->items = $items;
    }
}
