<?php

declare(strict_types=1);

namespace Soap\WsdlReader\OldStuff\Reader\Iterator;

use DOMElement;
use Generator;
use IteratorAggregate;
use Psl\Type;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Dom\Xpath;

final class PortIterator implements IteratorAggregate
{
    private Document $wsdl;

    public function __construct(Document $wsdl)
    {
        $this->wsdl = $wsdl;
    }

    public function getIterator(): Generator
    {
        $xpath = Xpath::fromDocument($this->wsdl, new WsdlPreset($this->wsdl));

        yield from array_reduce(
            [...$xpath->query('/wsdl:definitions/wsdl:portType')],
            static fn (array $ports, DOMElement $port): array => array_merge(
                $ports,
                [
                    $port->getAttribute('name') => [
                        'name' => $port->getAttribute('name'),
                        'operations' => array_reduce(
                            [...$xpath->query('./wsdl:operation', $port)],
                            static fn (array $bindings, DOMElement $operation) => array_merge(
                                $bindings,
                                [
                                    $operation->getAttribute('name') => [
                                        'name' => $operation->getAttribute('name'),
                                        'input' => [
                                            'name' => $xpath->evaluate('string(./wsdl:input/@name)', Type\string(), $operation),
                                            'message' => $xpath->evaluate('string(./wsdl:input/@message)', Type\string(), $operation),
                                        ],
                                        'output' => [
                                            'name' => $xpath->evaluate('string(./wsdl:output/@name)', Type\string(), $operation),
                                            'message' => $xpath->evaluate('string(./wsdl:output/@message)', Type\string(), $operation),
                                        ]
                                    ],
                                ]
                            ),
                            []
                        )
                    ],
                ]
            ),
            []
        );
    }
}
