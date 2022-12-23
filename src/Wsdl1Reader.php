<?php
declare(strict_types=1);

namespace Soap\WsdlReader;

use Soap\Wsdl\Loader\WsdlLoader;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Parser\Wsdl1Parser;
use VeeWee\Xml\Dom\Document;

class Wsdl1Reader
{
    public function __construct(
        private WsdlLoader $loader
    ){
    }

    public function __invoke(string $location): Wsdl1
    {
        $wsdlContent = ($this->loader)($location);
        $wsdlDocument = Document::fromXmlString(
            $wsdlContent,
            // TODO : Create new configurator...
            static function (\DOMDocument $document) use ($location): \DOMDocument {
                $document->documentURI = $location;
                return $document;
            }
        );

        return (new Wsdl1Parser())($wsdlDocument);
    }
}
