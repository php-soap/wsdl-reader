<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use Soap\Engine\Metadata\Collection\TypeCollection;

class SchemaToTypesConverter
{
    public function __invoke(Schema $schema): TypeCollection
    {
        // BASE ON
        // * https://github.com/goetas-webservices/wsdl2php/tree/master/src/Generation
        // * https://github.com/goetas-webservices/xsd2php/blob/master/src/AbstractConverter.php
        // * https://github.com/goetas-webservices/xsd2php/blob/master/src/Php/PhpConverter.php

        return new TypeCollection();
    }
}
