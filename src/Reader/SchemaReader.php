<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader as XsdReader;
use Soap\WsdlReader\Reader\Iterator\SchemaIterator;
use VeeWee\Xml\Dom\Document;

final class SchemaReader
{
    public function read(Document $wsdl): Schema
    {
        $reader = new XsdReader();

        return $reader->readNodes([...new SchemaIterator($wsdl)]);
    }
}
