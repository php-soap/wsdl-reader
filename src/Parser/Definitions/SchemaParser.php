<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

class SchemaParser
{
    public static function tryParse(Document $wsdl): Schema
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));

        return (new SchemaReader())->readNodes(
            [...$xpath->query('/wsdl:definitions/wsdl:types/schema:schema')]
        );
    }
}
