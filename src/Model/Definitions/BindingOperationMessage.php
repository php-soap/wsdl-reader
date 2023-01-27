<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Soap\WsdlReader\Model\Definitions\Implementation\Message\MessageImplementation;

final class BindingOperationMessage
{
    public function __construct(
        public readonly string $name,
        public readonly MessageImplementation $implementation,
    ) {
    }
}
