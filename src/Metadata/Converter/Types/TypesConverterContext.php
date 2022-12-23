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

class TypesConverterContext
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

        $new->addAliasMap($xsd, 'gYearMonth', fn () => 'int');
        $new->addAliasMap($xsd, 'gMonthDay', fn () => 'int');
        $new->addAliasMap($xsd, 'gMonth', fn () => 'int');
        $new->addAliasMap($xsd, 'gYear', fn () => 'int');
        $new->addAliasMap($xsd, 'NMTOKEN', fn () => 'string');
        $new->addAliasMap($xsd, 'NMTOKENS', fn () => 'string');
        $new->addAliasMap($xsd, 'QName', fn () => 'string');
        $new->addAliasMap($xsd, 'NCName', fn () => 'string');
        $new->addAliasMap($xsd, 'decimal', fn () => 'float');
        $new->addAliasMap($xsd, 'float', fn () => 'float');
        $new->addAliasMap($xsd, 'double', fn () => 'float');
        $new->addAliasMap($xsd, 'string', fn () => 'string');
        $new->addAliasMap($xsd, 'normalizedString', fn () => 'string');
        $new->addAliasMap($xsd, 'integer', fn () => 'int');
        $new->addAliasMap($xsd, 'int', fn () => 'int');
        $new->addAliasMap($xsd, 'unsignedInt', fn () => 'int');
        $new->addAliasMap($xsd, 'negativeInteger', fn () => 'int');
        $new->addAliasMap($xsd, 'positiveInteger', fn () => 'int');
        $new->addAliasMap($xsd, 'nonNegativeInteger', fn () => 'int');
        $new->addAliasMap($xsd, 'nonPositiveInteger', fn () => 'int');
        $new->addAliasMap($xsd, 'long', fn () => 'int');
        $new->addAliasMap($xsd, 'unsignedLong', fn () => 'int');
        $new->addAliasMap($xsd, 'short', fn () => 'int');
        $new->addAliasMap($xsd, 'boolean', fn () => 'bool');
        $new->addAliasMap($xsd, 'nonNegativeInteger', fn () => 'int');
        $new->addAliasMap($xsd, 'positiveInteger', fn () => 'int');
        $new->addAliasMap($xsd, 'language', fn () => 'string');
        $new->addAliasMap($xsd, 'token', fn () => 'string');
        $new->addAliasMap($xsd, 'anyURI', fn () => 'string');
        $new->addAliasMap($xsd, 'byte', fn () => 'string');
        $new->addAliasMap($xsd, 'duration', fn () => 'DateInterval');
        $new->addAliasMap($xsd, 'ID', fn () => 'string');
        $new->addAliasMap($xsd, 'IDREF', fn () => 'string');
        $new->addAliasMap($xsd, 'IDREFS', fn () => 'string');
        $new->addAliasMap($xsd, 'Name', fn () => 'string');
        $new->addAliasMap($xsd, 'NCName', fn () => 'string');
        $new->addAliasMap($xsd, 'dateTime', fn () => 'DateTime');
        $new->addAliasMap($xsd, 'time', fn () => 'DateTime');
        $new->addAliasMap($xsd, 'date', fn () => 'DateTime');
        $new->addAliasMap($xsd, 'anySimpleType', fn () => 'mixed');
        $new->addAliasMap($xsd, 'anyType', fn () => 'mixed');
        $new->addAliasMap($xsd, 'base64Binary', fn () => 'string');

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
     * @param Type $type
     * @return Option<string>
     */
    public function getTypeAlias(Type $type): Option
    {
        $schema = $type->getSchema();
        $mapper = $this->aliasMap[$schema->getTargetNamespace()][$type->getName()] ?? null;

        return $mapper ? some($mapper($type)) : none();
    }

    /**
     * @param Schema $schema
     * @param callable(Schema $schema): TypeCollection $visitor
     * @return TypeCollection
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
