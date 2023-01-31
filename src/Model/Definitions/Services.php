<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class Services
{
    /**
     * @var list<Service>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        Service ... $items
    ) {
        $this->items = $items;
    }
}
