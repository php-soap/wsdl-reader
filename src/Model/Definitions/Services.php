<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Services
{
    /**
     * @var list<Service>
     */
    public readonly array $items;

    public function __construct(
        Service ... $items
    ){
        $this->items = $items;
    }
}
