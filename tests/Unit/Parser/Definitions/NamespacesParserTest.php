<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Parser\Definitions;

use PHPUnit\Framework\TestCase;
use Soap\WsdlReader\Parser\Definitions\NamespacesParser;
use VeeWee\Xml\Dom\Document;

final class NamespacesParserTest extends TestCase
{
    private const XML = 'http://www.w3.org/XML/1998/namespace';

    public function test_it_knows_the_implicit_xml_prefix(): void
    {
        $namespaces = NamespacesParser::tryParse(Document::fromXmlString(
            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:tns="https://test" targetNamespace="https://test"/>'
        ));

        static::assertSame(self::XML, $namespaces->lookupNamespaceFromName('xml')->unwrap());
        static::assertSame('xml', $namespaces->lookupNameFromNamespace(self::XML)->unwrap());
        static::assertSame('https://test', $namespaces->lookupNamespaceFromName('tns')->unwrap());
    }

    public function test_it_keeps_an_explicit_xml_declaration(): void
    {
        $namespaces = NamespacesParser::tryParse(Document::fromXmlString(
            '<definitions xmlns="http://schemas.xmlsoap.org/wsdl/" xmlns:xml="' . self::XML . '"/>'
        ));

        static::assertSame(self::XML, $namespaces->lookupNamespaceFromName('xml')->unwrap());
        static::assertSame('xml', $namespaces->lookupNameFromNamespace(self::XML)->unwrap());
    }
}
