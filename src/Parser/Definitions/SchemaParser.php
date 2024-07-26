<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader;
use Soap\WsdlReader\Parser\Context\ParserContext;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\document_element;

final class SchemaParser
{
    /**
     * @var array<string, string>
     */
    public static array $knownSchemas = [
        'http://schemas.xmlsoap.org/wsdl/' => __DIR__.'/../../../resources/xsd/wsdl.xsd',
        'http://schemas.xmlsoap.org/soap/encoding/' => __DIR__.'/../../../resources/xsd/soap11-encoding.xsd',
        'http://www.w3.org/2001/09/soap-encoding' => __DIR__.'/../../../resources/xsd/soap12-encoding-2001.xsd',
        'http://www.w3.org/2003/05/soap-encoding' => __DIR__.'/../../../resources/xsd/soap12-encoding-2003.xsd',
        'http://xml.apache.org/xml-soap' => __DIR__.'/../../../resources/xsd/apache-xml-soap.xsd',
    ];

    public static function tryParse(Document $wsdl, ParserContext $context): Schema
    {
        $xpath = $wsdl->xpath(new WsdlPreset($wsdl));
        $reader = new SchemaReader();

        // Make sure to register the known schema locations and to import them globally.
        // This way, they can be used without expecting an explicit import from within the XSD.
        // Since WSDLs don't require the soap specific schema's to be imported.
        $globalSchema = $reader->getGlobalSchema();
        foreach ($context->knownSchemas as $namespace => $location) {
            $reader->addKnownNamespaceSchemaLocation($namespace, 'file://'.$location);
            $globalSchema->addSchema(
                $reader->readNode(Document::fromXmlFile($location)->locate(document_element()), $namespace)
            );
        }

        return $reader->readNodes(
            [...$xpath->query('/wsdl:definitions/wsdl:types/schema:schema')],
            $wsdl->toUnsafeDocument()->documentURI
        );
    }
}
