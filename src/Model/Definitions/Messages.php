<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Messages
{
    /**
     * @var list<Message>
     */
    public readonly array $items;

    public function __construct(
        Message ... $items
    ){
        $this->items = $items;
    }
}
