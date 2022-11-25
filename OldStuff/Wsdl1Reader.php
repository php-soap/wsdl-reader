<?php

declare(strict_types=1);

namespace Soap\WsdlReader\OldStuff\OldStuff;

use Soap\Engine\Metadata\LazyInMemoryMetadata;
use Soap\Engine\Metadata\Metadata;
use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\OldStuff\OldStuff\Metadata\Wsdl1Metadata;
use VeeWee\Xml\Dom\Document;

final class Wsdl1Reader
{
    public function __construct(
        private WsdlLoader $loader
    ) {
    }

    public function __invoke(string $wsdlLocation): Metadata
    {
        $wsdl = ($this->loader)($wsdlLocation);
        $doc = Document::fromXmlString($wsdl);

        return new LazyInMemoryMetadata(new Wsdl1Metadata($doc));
    }
}
