<?php

declare(strict_types=1);

namespace Soap\WsdlReader;

use Soap\WsdlReader\Loader\Loader;
use Soap\WsdlReader\Metadata\MetadataInterface;
use Soap\WsdlReader\Metadata\Provider\WsdlReadingMetadataProvider;
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

    public static function fromParser(Parser $parser): self
    {
        return new self($parser);
    }

    public function read(string $wsdlLocation): MetadataInterface
    {
        $wsdl = $this->parser->parse($wsdlLocation);

        return (new WsdlReadingMetadataProvider($wsdl))->getMetadata();
    }
}
