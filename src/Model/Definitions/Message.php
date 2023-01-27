<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class Message
{
    public function __construct(
        public readonly string $name,
        public readonly Parts $parts
    ) {
    }
}
