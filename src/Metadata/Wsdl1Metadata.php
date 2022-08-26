<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata;

use Soap\Engine\Metadata\Collection\MethodCollection;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Metadata;
use Soap\WsdlReader\Reader\MethodsReader;
use Soap\WsdlReader\Reader\SchemaReader;
use Soap\WsdlReader\Reader\ServiceReader;
use Soap\WsdlReader\Reader\TypesReader;
use Soap\WsdlReader\Schema\TypeProvider;
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
