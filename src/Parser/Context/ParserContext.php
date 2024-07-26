<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Parser\Context;

use Soap\WsdlReader\Parser\Definitions\SchemaParser;

final class ParserContext
{
    /**
     * @param array<string, string> $knownSchemas - A dictionary of the xml namespace and a locally known XSD file.
     */
    public function __construct(
        public array $knownSchemas,
    ) {
    }

    public static function defaults(): self
    {
        return new self(
            knownSchemas: SchemaParser::$knownSchemas,
        );
    }

    public function withAdditionalKnownSchema(string $namespace, string $xsdLocation): self
    {
        $new = clone $this;
        $new->knownSchemas = [...$this->knownSchemas, $namespace => $xsdLocation];

        return $new;
    }
}
