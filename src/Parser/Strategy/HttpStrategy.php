<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Strategy;

use DOMElement;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\BindingImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\HttpBinding;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\HttpMessage;
use Soap\WsdlReader\Model\Definitions\Implementation\Message\MessageImplementation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\HttpOperation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\OperationImplementation;
use Soap\WsdlReader\Model\Definitions\TransportType;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\Element\children;

final class HttpStrategy implements StrategyInterface
{
    private const HTTP_NAMESPACE = 'http://schemas.xmlsoap.org/wsdl/http/';
    private const MIME_NAMESPACE = 'http://schemas.xmlsoap.org/wsdl/mime/';

    public function parseBindingImplementation(Document $wsdl, DOMElement $binding): BindingImplementation
    {
        return new HttpBinding(
            verb: $binding->getAttribute('verb'),
            transport: TransportType::tryFrom((string) $binding->namespaceURI) ?? TransportType::HTTP,
        );
    }

    public function parseOperationImplementation(Document $wsdl, DOMElement $operation): OperationImplementation
    {
        return new HttpOperation(
            location: $operation->getAttribute('location'),
        );
    }

    public function parseMessageImplementation(Document $wsdl, DOMElement $message): MessageImplementation
    {
        $info = children($message)->first();
        $fallbackImplementation = new HttpMessage(
            contentType: 'application/xml',
            part: null
        );

        if (!$info) {
            return $fallbackImplementation;
        }

        return match ($info->namespaceURI) {
            self::HTTP_NAMESPACE => new HttpMessage(
                contentType: 'text/plain',
                part: null
            ),
            self::MIME_NAMESPACE => new HttpMessage(
                contentType: match($info->localName) {
                    'content' => $info->hasAttribute('type') ? $info->getAttribute('type'): 'application/xml',
                    'mimeXml' => 'application/xml',
                    'multipartRelated' => 'Multipart/Related',
                    default => 'application/xml'
                },
                part: $info->hasAttribute('part') ? $info->getAttribute('part') : null,
            ),
            default => $fallbackImplementation,
        };
    }
}
