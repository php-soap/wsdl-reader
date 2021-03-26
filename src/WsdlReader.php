<?php

declare(strict_types=1);

namespace Soap\WsdlReader;

use Soap\WsdlReader\Loader\Loader;
use Soap\WsdlReader\Xml\Parser;

final class WsdlReader
{
    private function __construct(
        private Parser $parser
    ) {
    }

    public function fromLoader(Loader $loader): self
    {
        return new self(
            new Parser($loader)
        );
    }

    public function read(string $wsdlLocation)
    {

    }
}
