<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader\Iterator;

use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Dom\Xpath;

class SchemaIterator implements \IteratorAggregate
{
    private Document $wsdl;

    public function __construct(Document $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getIterator(): \Generator
    {
        $xpath = Xpath::fromDocument($this->wsdl, new WsdlPreset($this->wsdl));

        yield from [...$xpath->query('/wsdl:definitions/wsdl:types/schema:schema')];
    }
}
