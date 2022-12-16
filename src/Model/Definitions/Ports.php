<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Ports
{
    /**
     * @var list<Port>
     */
    public readonly array $items;

    public function __construct(
        Port ... $items
    ){
        $this->items = $items;
    }
}
