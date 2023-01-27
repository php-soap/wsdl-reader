<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types;

use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Psl\Option\Option;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Parser\Definitions\SchemaParser;
use Soap\Xml\Xmlns;
use function Psl\Option\none;
use function Psl\Option\some;

final class TypesConverterContext
{
    private static array $baseSchemas = [
        'http://www.w3.org/2001/XMLSchema',
        'http://www.w3.org/XML/1998/namespace',
    ];

    /**
     * Contains a namespaced dictionary that contains a dictionary of type name -> mapper
     * @var array<string, array<string, callable(Type $type): string>>
     */
    private array $aliasMap = [];

    /**
     * @var array<string, true>
     */
    private array $visited = [];

    private function __construct(
    ) {
    }

    public static function default(): self
    {
        $new = new self();
        $xsd = Xmlns::xsd()->value();

        $new->addAliasMap($xsd, 'gYearMonth', static fn () => 'int');
        $new->addAliasMap($xsd, 'gMonthDay', static fn () => 'int');
        $new->addAliasMap($xsd, 'gMonth', static fn () => 'int');
        $new->addAliasMap($xsd, 'gYear', static fn () => 'int');
        $new->addAliasMap($xsd, 'NMTOKEN', static fn () => 'string');
        $new->addAliasMap($xsd, 'NMTOKENS', static fn () => 'string');
        $new->addAliasMap($xsd, 'QName', static fn () => 'string');
        $new->addAliasMap($xsd, 'NCName', static fn () => 'string');
        $new->addAliasMap($xsd, 'decimal', static fn () => 'float');
        $new->addAliasMap($xsd, 'float', static fn () => 'float');
        $new->addAliasMap($xsd, 'double', static fn () => 'float');
        $new->addAliasMap($xsd, 'string', static fn () => 'string');
        $new->addAliasMap($xsd, 'normalizedString', static fn () => 'string');
        $new->addAliasMap($xsd, 'integer', static fn () => 'int');
        $new->addAliasMap($xsd, 'int', static fn () => 'int');
        $new->addAliasMap($xsd, 'unsignedInt', static fn () => 'int');
        $new->addAliasMap($xsd, 'negativeInteger', static fn () => 'int');
        $new->addAliasMap($xsd, 'positiveInteger', static fn () => 'int');
        $new->addAliasMap($xsd, 'nonNegativeInteger', static fn () => 'int');
        $new->addAliasMap($xsd, 'nonPositiveInteger', static fn () => 'int');
        $new->addAliasMap($xsd, 'long', static fn () => 'int');
        $new->addAliasMap($xsd, 'unsignedLong', static fn () => 'int');
        $new->addAliasMap($xsd, 'short', static fn () => 'int');
        $new->addAliasMap($xsd, 'boolean', static fn () => 'bool');
        $new->addAliasMap($xsd, 'nonNegativeInteger', static fn () => 'int');
        $new->addAliasMap($xsd, 'positiveInteger', static fn () => 'int');
        $new->addAliasMap($xsd, 'language', static fn () => 'string');
        $new->addAliasMap($xsd, 'token', static fn () => 'string');
        $new->addAliasMap($xsd, 'anyURI', static fn () => 'string');
        $new->addAliasMap($xsd, 'byte', static fn () => 'string');
        $new->addAliasMap($xsd, 'duration', static fn () => 'DateInterval');
        $new->addAliasMap($xsd, 'ID', static fn () => 'string');
        $new->addAliasMap($xsd, 'IDREF', static fn () => 'string');
        $new->addAliasMap($xsd, 'IDREFS', static fn () => 'string');
        $new->addAliasMap($xsd, 'Name', static fn () => 'string');
        $new->addAliasMap($xsd, 'NCName', static fn () => 'string');
        $new->addAliasMap($xsd, 'dateTime', static fn () => 'DateTime');
        $new->addAliasMap($xsd, 'time', static fn () => 'DateTime');
        $new->addAliasMap($xsd, 'date', static fn () => 'DateTime');
        $new->addAliasMap($xsd, 'anySimpleType', static fn () => 'mixed');
        $new->addAliasMap($xsd, 'anyType', static fn () => 'mixed');
        $new->addAliasMap($xsd, 'base64Binary', static fn () => 'string');

        return $new;
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
     * @param callable(Type $type): string $mapper
     */
    public function addAliasMap(string $namespace, string $name, callable $mapper): self
    {
        $this->aliasMap[$namespace][$name] = $mapper;

        return $this;
    }

    /**
     * @return Option<string>
     */
    public function getTypeAlias(Type $type): Option
    {
        $schema = $type->getSchema();
        $mapper = $this->aliasMap[$schema->getTargetNamespace()][$type->getName()] ?? null;

        return $mapper ? some($mapper($type)) : none();
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
}
