<?php declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Paser\Strategy;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\HttpMessage;
use Soap\WsdlReader\Parser\Strategy\HttpStrategy;
use VeeWee\Xml\Dom\Document;

final class HttpStrategyTest extends TestCase
{

    #[Test]
    #[DataProvider('provideMessages')]
    public function it_can_parse_message_implementations(string $xml, HttpMessage $expected): void
    {
        $doc = Document::fromXmlString($xml);
        $element = $doc->locateDocumentElement();

        $strategy = new HttpStrategy();
        $actual = $strategy->parseMessageImplementation($doc, $element);

        static::assertEquals($expected, $actual);
    }

    public static function provideMessages(): iterable
    {
        yield 'no-info' => [
            <<<EOXML
            <wsdl:input
                  xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/" />
            EOXML,
            new HttpMessage(
                contentType: 'application/xml',
                part: null
            )
        ];

        yield 'mime-content-info' => [
            <<<EOXML
            <wsdl:input
                xmlns:http="http://schemas.xmlsoap.org/wsdl/http/"
                xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/"
                xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
                    <mime:content part="CancelBookingRequest" type="application/soap+xml"/>
            </wsdl:input>
            EOXML,
            new HttpMessage(
                contentType: 'application/soap+xml',
                part: 'CancelBookingRequest'
            )
        ];

        yield 'mime-xml' => [
            <<<EOXML
            <wsdl:input
                xmlns:http="http://schemas.xmlsoap.org/wsdl/http/"
                xmlns:mime="http://schemas.xmlsoap.org/wsdl/mime/"
                xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
                    <mime:mimeXml part="getServiceTicketResponsePart"/>
            </wsdl:input>
            EOXML,
            new HttpMessage(
                contentType: 'application/xml',
                part: 'getServiceTicketResponsePart'
            )
        ];
        yield 'http-url-encoded' => [
            <<<EOXML
            <wsdl:input
                xmlns:http="http://schemas.xmlsoap.org/wsdl/http/"
                xmlns:wsdl="http://schemas.xmlsoap.org/wsdl/">
                    <http:urlEncoded/>
            </wsdl:input>
            EOXML,
            new HttpMessage(
                contentType: 'text/plain',
                part: null
            )
        ];
    }
}
