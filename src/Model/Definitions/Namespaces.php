<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class Namespaces
{
    /**
     * @param array<string, string> $items
     */
    public function __construct(
        public readonly array $items
    ){
    }
}
