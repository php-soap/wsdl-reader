<?php
declare(strict_types=1);

namespace Soap\WsdlReader\OldStuff\OldStuff\Metadata;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Metadata;
use Soap\WsdlReader\OldStuff\OldStuff\Reader\MethodsReader;
use Soap\WsdlReader\OldStuff\OldStuff\Reader\SchemaReader;
use Soap\WsdlReader\OldStuff\OldStuff\Reader\ServiceReader;
use Soap\WsdlReader\OldStuff\OldStuff\Reader\TypesReader;
use Soap\WsdlReader\OldStuff\OldStuff\Schema\TypeProvider;
use VeeWee\Xml\Dom\Document;

final class Wsdl1Metadata implements Metadata
{
    public function __construct(
        private Document $wsdl
    ) {
    }

    public function getTypes(): TypeCollection
    {
        return (new TypesReader(new SchemaReader(), new TypeProvider()))->read($this->wsdl);
    }

    public function getMethods(): MethodCollection
    {
        return (new MethodsReader(new ServiceReader()))->read($this->wsdl);
    }
}
