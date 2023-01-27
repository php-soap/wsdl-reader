<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

final class Address
{
    public function __construct(
        public readonly SoapVersion $soapVersion,
        public readonly string $location
    ) {
    }
}
