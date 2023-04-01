<?php
declare(strict_types=1);

namespace Soap\WsdlReader;

use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Parser\Context\ParserContext;
use Soap\WsdlReader\Parser\Wsdl1Parser;
use VeeWee\Xml\Dom\Document;
use function Psl\Type\non_empty_string;
use function VeeWee\Xml\Dom\Configurator\document_uri;

final class Wsdl1Reader
{
    public function __construct(
        private WsdlLoader $loader
    ) {
    }

    /**
     * @param non-empty-string $location
     */
    public function __invoke(string $location, ?ParserContext $context = null): Wsdl1
    {
        $context ??= ParserContext::defaults();
        $wsdlContent = ($this->loader)($location);
        $wsdlDocument = Document::fromXmlString(
            non_empty_string()->assert($wsdlContent),
            document_uri($location)
        );

        return (new Wsdl1Parser())($wsdlDocument, $context);
    }
}
