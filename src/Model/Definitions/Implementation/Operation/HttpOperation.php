<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Operation;

use Soap\WsdlReader\Model\Definitions\TransportType;

final class HttpOperation implements OperationImplementation
{
    public function __construct(
        public readonly string $location,
        public readonly TransportType $transport,
    ) {
    }
}
