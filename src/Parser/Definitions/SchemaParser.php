<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\SchemaReader;
use Soap\WsdlReader\Parser\Context\ParserContext;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\ErrorHandling\disallow_libxml_false_returns;

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
                $reader->readNode(self::legacyDocumentElement($location), $namespace)
            );
        }

        return $reader->readNodes(
            self::legacySchemaNodes($wsdl),
            $wsdl->toUnsafeDocument()->documentURI
        );
    }

    /**
     * TODO: Can be removed once goetas-webservices/xsd-reader supports Dom\Element.
     *
     * @return list<DOMElement>
     */
    private static function legacySchemaNodes(Document $wsdl): array
    {
        $legacyDoc = $wsdl->toUnsafeLegacyDocument();
        $legacyXpath = new DOMXPath($legacyDoc);
        $legacyXpath->registerNamespace('wsdl', 'http://schemas.xmlsoap.org/wsdl/');
        $legacyXpath->registerNamespace('schema', 'http://www.w3.org/2001/XMLSchema');

        /** @var DOMNodeList<DOMNode> $nodes */
        $nodes = disallow_libxml_false_returns(
            $legacyXpath->query('/wsdl:definitions/wsdl:types/schema:schema'),
            'Unable to query schema nodes from legacy document'
        );

        $result = [];
        foreach ($nodes as $node) {
            assert($node instanceof DOMElement);
            $result[] = $node;
        }

        return $result;
    }

    /**
     * TODO: Can be removed once goetas-webservices/xsd-reader supports Dom\Element.
     */
    private static function legacyDocumentElement(string $location): DOMElement
    {
        $doc = new DOMDocument();
        disallow_libxml_false_returns(
            $doc->load($location),
            'Unable to load XML file: '.$location
        );

        assert($doc->documentElement instanceof DOMElement);

        return $doc->documentElement;
    }
}
