<?php

declare(strict_types=1);

namespace Soap\WsdlReader;

use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Metadata\MetadataInterface;
use Soap\WsdlReader\Metadata\Provider\WsdlReadingMetadataProvider;
use VeeWee\Xml\Dom\Document;

final class WsdlReader
{
    private function __construct(
        private WsdlLoader $loader
    ) {
    }

    public static function fromLoader(WsdlLoader $loader): self
    {
        return new self($loader);
    }

    public function read(string $wsdlLocation): MetadataInterface
    {
        $wsdl = ($this->loader)($wsdlLocation);
        $doc = Document::fromXmlString($wsdl);

        return (new WsdlReadingMetadataProvider($doc))->getMetadata();
    }
}
