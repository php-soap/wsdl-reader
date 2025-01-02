<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use Psl\Option\Option;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Model\Definitions\Namespaces;
use Soap\WsdlReader\Parser\Definitions\SchemaParser;
use function Psl\Option\from_nullable;
use function Psl\Option\none;

final class TypesConverterContext
{
    private static array $baseSchemas = [
        'http://www.w3.org/2001/XMLSchema',
        'http://www.w3.org/XML/1998/namespace',
    ];

    /**
     * @var array<string, true>
     */
    private array $visited = [];

    /**
     * @var Option<ParentContext>
     */
    private Option $parentContext;

    private function __construct(
        public readonly Namespaces $knownNamespaces
    ) {
        $this->parentContext = none();
    }

    public static function default(Namespaces $knownNamespaces): self
    {
        return new self($knownNamespaces);
    }

    public function isBaseSchema(Schema $schema): bool
    {
        return in_array(
            $schema->getTargetNamespace(),
            [...self::$baseSchemas, ...array_keys(SchemaParser::$knownSchemas)],
            true
        );
    }

    /**
     * @param callable(Schema $schema): TypeCollection $visitor
     */
    public function visit(Schema $schema, callable $visitor): TypeCollection
    {
        if ($this->isBaseSchema($schema)) {
            return new TypeCollection();
        }

        $schemaHash = spl_object_hash($schema);
        if (array_key_exists($schemaHash, $this->visited)) {
            return new TypeCollection();
        }

        $this->visited[$schemaHash] = true;

        return $visitor($schema);
    }

    public function onParent(?ParentContext $parentContext): self
    {
        $this->parentContext = from_nullable($parentContext);

        return $this;
    }

    /**
     * @return Option<ParentContext>
     */
    public function parent(): Option
    {
        return $this->parentContext;
    }
}
