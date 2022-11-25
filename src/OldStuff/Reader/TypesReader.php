<?php

declare(strict_types=1);

namespace Soap\WsdlReader\OldStuff\Reader;

use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\OldStuff\Schema\TypeProvider;
use VeeWee\Xml\Dom\Document;

final class TypesReader
{
    private SchemaReader $schemaReader;
    private TypeProvider $typeProvider;

    public function __construct(SchemaReader $schemaReader, TypeProvider $typeProvider)
    {
        $this->schemaReader = $schemaReader;
        $this->typeProvider = $typeProvider;
    }

    public function read(Document $wsdl): TypeCollection
    {
        $schema = $this->schemaReader->read($wsdl);
        $types = [...$this->typeProvider->forSchema($schema)];

        return new TypeCollection(...$types);
    }
}
