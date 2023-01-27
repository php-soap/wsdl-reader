<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class Port
{
    public function __construct(
        public readonly string $name,
        public readonly QNamed $binding,
        public readonly Address $address
    ) {
    }
}
