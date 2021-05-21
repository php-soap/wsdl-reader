<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Provider;

use Soap\WsdlReader\Metadata\Metadata;
use Soap\WsdlReader\Metadata\MetadataInterface;
use Soap\WsdlReader\Metadata\MetadataProviderInterface;
use Soap\WsdlReader\Reader\MethodsReader;
use Soap\WsdlReader\Reader\SchemaReader;
use Soap\WsdlReader\Reader\ServiceReader;
use Soap\WsdlReader\Reader\TypesReader;
use Soap\WsdlReader\Schema\TypeProvider;
use VeeWee\Xml\Dom\Document;

class WsdlReadingMetadataProvider implements MetadataProviderInterface
{
    public function __construct(
        private Document $wsdl
    ) {
    }

    public function getMetadata(): MetadataInterface
    {
        return new Metadata(
            (new TypesReader(new SchemaReader(), new TypeProvider()))->read($this->wsdl),
            (new MethodsReader(new ServiceReader()))->read($this->wsdl)
        );
    }
}
