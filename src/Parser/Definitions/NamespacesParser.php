<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Definitions;

use DOMNameSpaceNode;
use Soap\WsdlReader\Model\Definitions\Namespaces;
use VeeWee\Xml\Dom\Document;
use function Psl\Dict\merge;
use function VeeWee\Xml\Dom\Locator\document_element;
use function VeeWee\Xml\Dom\Locator\Xmlns\recursive_linked_namespaces;

final class NamespacesParser
{
    public static function tryParse(Document $wsdl): Namespaces
    {
        $root = $wsdl->map(document_element());
        $allNamespaces = recursive_linked_namespaces($root);

        return new Namespaces(
            $allNamespaces->reduce(
                /**
                 * @param array<string, string> $map
                 * @return array<string, string>
                 */
                static fn (array $map, DOMNameSpaceNode $node): array
                    => merge($map, [(string)$node->localName => (string)$node->namespaceURI]),
                []
            ),
            $allNamespaces->reduce(
                /**
                 * @param array<string, string> $map
                 * @return array<string, string>
                 */
                static fn (array $map, DOMNameSpaceNode $node): array
                    => merge($map, [(string)$node->namespaceURI => (string)$node->localName]),
                []
            )
        );
    }
}
