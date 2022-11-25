<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Bindings
{
    /**
     * @var list<Binding>
     */
    public readonly array $items;

    public function __construct(
        Binding ... $items
    ){
        $this->items = $items;
    }
}
