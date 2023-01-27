<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;

final class SchemaParser
{
    public static array $knownSchemas = [
        'http://schemas.xmlsoap.org/wsdl/' => __DIR__.'/../../../resources/xsd/wsdl.xsd',
        'http://schemas.xmlsoap.org/soap/encoding/' => __DIR__.'/../../../resources/xsd/soap11-encoding.xsd',
        'http://www.w3.org/2001/09/soap-encoding' => __DIR__.'/../../../resources/xsd/soap12-encoding-2001.xsd',
        'http://www.w3.org/2003/05/soap-encoding' => __DIR__.'/../../../resources/xsd/soap12-encoding-2003.xsd',
        'http://xml.apache.org/xml-soap' => __DIR__.'/../../../resources/xsd/apache-xml-soap.xsd',
    ];

    public static function tryParse(Document $wsdl): Schema
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $reader = new SchemaReader();

        // TODO : configurable from the outside?
        foreach (self::$knownSchemas as $namespace => $location) {
            $reader->addKnownNamespaceSchemaLocation($namespace, $location);
        }

        return $reader->readNodes(
            [...$xpath->query('/wsdl:definitions/wsdl:types/schema:schema')]
        );
    }
}
