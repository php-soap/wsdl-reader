<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class PortTypes
{
    /**
     * @var list<PortType>
     */
    public readonly array $items;

    public function __construct(
        PortType ... $items
    ){
        $this->items = $items;
    }
}
