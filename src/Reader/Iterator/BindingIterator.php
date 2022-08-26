<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Reader\Iterator;

use DOMElement;
use Generator;
use IteratorAggregate;
use Psl\Type;
use Soap\Xml\Xpath\WsdlPreset;
use VeeWee\Xml\Dom\Document;
use VeeWee\Xml\Dom\Xpath;

final class BindingIterator implements IteratorAggregate
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
            [...$xpath->query('/wsdl:definitions/wsdl:binding')],
            static fn (array $bindings, DOMElement $binding): array => array_merge(
                $bindings,
                [
                    $binding->getAttribute('name') => [
                        'name' => $binding->getAttribute('name'),
                        'type' => $binding->getAttribute('type'),
                        'transport' => $xpath->evaluate('string(./soap:binding/@transport)', Type\string(), $binding),
                        'operations' => array_reduce(
                            [...$xpath->query('./wsdl:operation', $binding)],
                            static fn (array $operations, DOMElement $operation): array => array_merge(
                                $operations,
                                [
                                    $operation->getAttribute('name') => [
                                        'name' => $operation->getAttribute('name'),
                                        'soapAction' => $xpath->evaluate('string(./soap:operation/@soapAction)', Type\string(), $operation),
                                        'style' => $xpath->evaluate('string(./soap:operation/@style)', Type\string(), $operation),
                                        'input' => [
                                            'name' => $xpath->evaluate('string(./wsdl:input/@name)', Type\string(), $operation),
                                            'bodyUse' => $xpath->evaluate('string(./wsdl:input/soap:body/@use)', Type\string(), $operation),
                                        ],
                                        'output' => [
                                            'name' => $xpath->evaluate('string(./wsdl:output/@name)', Type\string(), $operation),
                                            'bodyUse' => $xpath->evaluate('string(./wsdl:output/soap:body/@use)', Type\string(), $operation),
                                        ]
                                    ],
                                ]
                            ),
                            []
                        ),
                    ]
                ]
            ),
            []
        );
    }
}
