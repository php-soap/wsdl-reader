<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Operation;

final class HttpOperation implements OperationImplementation
{
    public function __construct(
        public readonly string $location,
    ) {
    }
}
