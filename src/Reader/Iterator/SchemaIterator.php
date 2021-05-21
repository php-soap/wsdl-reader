<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader\Iterator;

use Soap\WsdlReader\Xml\Xpath\XpathProvider;
use VeeWee\Xml\Dom\Document;

class SchemaIterator implements \IteratorAggregate
{
    private Document $wsdl;

    public function __construct(Document $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getIterator(): \Generator
    {
        $xpath = XpathProvider::provide($this->wsdl);

        yield from [...$xpath->query('/wsdl:definitions/wsdl:types/schema:schema')];
    }
}
