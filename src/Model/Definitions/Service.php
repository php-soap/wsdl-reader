<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Service
{
    public function __construct(
        public readonly string $name,
        public readonly Port $port,
        public readonly Address $address
    ){
    }
}
