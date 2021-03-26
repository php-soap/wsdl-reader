<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml\Configurator;

use DOMDocument;
use Soap\WsdlReader\Xml\Parser;
use Soap\WsdlReader\Xml\Paths\IncludePathBuilder;
use Soap\WsdlReader\Xml\Xpath\XpathProvider;
use VeeWee\Xml\Dom\Configurator\Configurator;
use VeeWee\Xml\Dom\Document;
use function VeeWee\Xml\Dom\Locator\document_element;
use function VeeWee\Xml\Dom\Locator\Node\children;
use function VeeWee\Xml\Dom\Manipulator\Node\replace_by_external_nodes;

final class FlattenWsdlImports implements Configurator
{
    public function __construct(
        private Parser $parser,
        private string $currentLocation
    ) {
    }

    /**
     * This method flattens wsdl:import locations.
     * It loads the WSDL and adds the definitions replaces the import tag with the definition children from the external file.
     *
     * For now, we don't care about the namespace property on the wsdl:import tag.
     * Future reference:
     * @link http://itdoc.hitachi.co.jp/manuals/3020/30203Y2310e/EY230669.HTM#ID01496
     */
    public function __invoke(DOMDocument $document): DOMDocument
    {
        $xpath = XPathProvider::provide(Document::fromUnsafeDocument($document));
        /** @var \DOMNodeList<\DOMElement> $imports */
        $imports = $xpath->query('wsdl:import');

        foreach ($imports as $import) {
            $location = IncludePathBuilder::build(
                $import->getAttribute('location'),
                $this->currentLocation
            );

            $definitions = $this->parser->parse($location)->map(document_element());

            replace_by_external_nodes(
                $import,
                children($definitions)
            );
        }

        return $document;
    }
}
