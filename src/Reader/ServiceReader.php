<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader;

use DOMNameSpaceNode;
use RuntimeException;
use Soap\WsdlReader\Parser\QnameParser;
use Soap\WsdlReader\Reader\Iterator\BindingIterator;
use Soap\WsdlReader\Reader\Iterator\MessageIterator;
use Soap\WsdlReader\Reader\Iterator\PortIterator;
use Soap\WsdlReader\Reader\Iterator\ServiceIterator;
use VeeWee\Xml\Dom\Document;
use function Psl\Dict\merge;
use function VeeWee\Xml\Dom\Locator\document_element;
use function VeeWee\Xml\Dom\Locator\Xmlns\recursive_linked_namespaces;

/**
 * TODO : Currently doesn't consider namespaced binding lookup...
 */
final class ServiceReader
{
    public function read(Document $wsdl): array
    {
        $services = new ServiceIterator($wsdl);
        $ports = iterator_to_array(new PortIterator($wsdl), true);
        $bindings = iterator_to_array(new BindingIterator($wsdl), true);
        $messages = iterator_to_array(new MessageIterator($wsdl), true);
        $namespaces = recursive_linked_namespaces($wsdl->map(document_element()));
        $parseQname = new QnameParser();

        foreach ($services as $service) {
            var_dump($service);
            [$bindingNamespace, $requiredBinding] = $parseQname($service['port']['binding']);
            $binding = $bindings[$requiredBinding] ?? null;
            [$portNamespace, $requiredPort] = $parseQname($binding['type']);
            $port = $ports[$requiredPort] ?? null;

            if (!$binding || !$port) {
                continue;
            }

            return [
                'service' => $service,
                'port' => $port,
                'binding' => $binding,
                'messages' => $messages,
                'namespaceMap' => $namespaces->reduce(
                    static fn (array $map, DOMNameSpaceNode $node): array
                        => merge($map, [$node->localName => $node->namespaceURI]),
                    []
                ),
            ];
        }

        throw new RuntimeException('Parsing WSDL: Couldn\'t bind to any service');
    }
}
