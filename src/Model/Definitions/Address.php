<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Address
{
    public function __construct(
        public readonly string $location
    ){
    }
}
