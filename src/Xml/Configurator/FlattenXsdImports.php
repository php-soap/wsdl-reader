<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml\Configurator;

use DOMDocument;
use DOMElement;
use DOMNodeList;
use Soap\WsdlReader\Xml\Exception\FlattenException;
use Soap\WsdlReader\Xml\Parser;
use Soap\WsdlReader\Xml\Paths\IncludePathBuilder;
use Soap\WsdlReader\Xml\Xmlns;
use Soap\WsdlReader\Xml\Xpath\XpathProvider;
use VeeWee\Xml\Dom\Configurator\Configurator;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Builder\namespaced_element;
use function VeeWee\Xml\Dom\Locator\Node\children;
use function VeeWee\Xml\Dom\Manipulator\Node\append_external_node;

final class FlattenXsdImports implements Configurator
{
    public function __construct(
        private Parser $parser,
        private string $currentLocation
    ) {
    }

    public function __invoke(DOMDocument $document): DOMDocument
    {
        $xpath = XPathProvider::provide(Document::fromUnsafeDocument($document));
        /** @var DOMNodeList<DOMElement> $imports */
        $imports = $xpath->query('//schema:import|//schema:include|//schema:redefine');
        if (!count($imports)) {
            return $document;
        }

        $types = $this->detectTypesContainer($document);

        foreach ($imports as $import) {
            $schemas = match ($import->localName) {
                'include', 'redefine' => $this->includeSchema($import),
                'import' => $this->importSchema($document, $import),
            };

            foreach ($schemas as $schema) {
                append_external_node($types, $schema);
            }
        }

        return $document;
    }

    /**
     * @return iterable<DOMElement>
     */
    private function includeSchema(DOMElement $include): iterable
    {
        if (!$location = $include->getAttribute('schemaLocation')) {
            throw FlattenException::noLocation($include->localName);
        }

        $schemas = $this->loadSchemas($location);
        $include->remove();

        return $schemas;
    }

    /**
     * @return iterable<DOMElement>
     */
    private function importSchema(DOMDocument $wsdl, DOMElement $import): iterable
    {
        $location = $import->getAttribute('schemaLocation');
        $namespace = $import->getAttribute('namespace');
        $tns = $wsdl->documentElement->getAttribute('targetNamespace');

        // Imports can only deal with different namespaces.
        // You'll need to use "include" if you want to inject something in the same namespace.
        if ($tns && $namespace && $tns === $namespace) {
            throw FlattenException::unableToImportXsd($location);
        }

        // xsd:import tags don't require a location!
        if (!$location) {
            return [];
        }

        $schemas = $this->loadSchemas($location);
        $import->removeAttribute('schemaLocation');

        return $schemas;
    }

    /**
     *
     * @return iterable<DOMElement>
     */
    private function loadSchemas(string $location): iterable
    {
        $path = IncludePathBuilder::build($location, $this->currentLocation);
        $document = $this->parser->parse($path)->toUnsafeDocument();

        // TODO : Check this!
        /** @var DOMNodeList<DOMElement> $schemas */
        $schemas = [...children($document)];

        return $schemas;
    }

    /**
     * For regular WSDL definitions, it will try to search for wsdl:types or create it.
     * If multiple wsdl:types elements exist, it will flatten these.
     *
     * For XSD schema files, it will append multiple schemas to the dom document
     * so that an upstream flatten XSD import action will include multiple XSD schemas.
     */
    private function detectTypesContainer(DOMDocument $document): DOMDocument|DOMElement
    {
        // Detect nested schema loads:
        if ($document->documentElement->localName === 'schema') {
            return $document;
        }

        $xpath = XpathProvider::provide(Document::fromUnsafeDocument($document));
        /** @var list<DOMElement> $typeslist */
        $typeslist = [...$xpath->query('wsdl:types')];

        // Creates wsdl:types if no matching element exists yet
        if (!$typeslist) {
            $document->documentElement->append(
                $types = namespaced_element(Xmlns::wsdl()->value(), 'types')($document)
            );

            return $types;
        }

        // Return first wsdl:types if there is only one element available:
        $first = array_pop($typeslist);
        if (!$typeslist) {
            return $first;
        }

        // Flattens multiple wsdl:types elements.
        foreach ($typeslist as $additionalTypes) {
            $children = $additionalTypes->childNodes;
            if (count($children)) {
                $first->append(...$children->getIterator());
            }

            $additionalTypes->remove();
        }

        return $first;
    }
}
