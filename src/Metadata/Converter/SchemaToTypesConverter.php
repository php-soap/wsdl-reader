<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementDef;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use Soap\WsdlReader\Metadata\Converter\Types\Visitor\ElementVisitor;
use Soap\WsdlReader\Metadata\Converter\Types\Visitor\TypeVisitor;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\flat_map;

final class SchemaToTypesConverter
{
    public function __invoke(Schema $schema, TypesConverterContext $context): TypeCollection
    {
        return $context->visit($schema, function () use ($schema, $context): TypeCollection {
            return new TypeCollection(
                ...filter_nulls([
                    ...flat_map(
                        $schema->getTypes(),
                        static fn (Type $type): TypeCollection => (new TypeVisitor())($type, $context)
                    ),
                    ...flat_map(
                        $schema->getElements(),
                        static fn (ElementDef $element): TypeCollection => (new ElementVisitor())($element, $context)
                    ),
                    ...flat_map(
                        $schema->getSchemas(),
                        fn (Schema $childSchema): TypeCollection => $this->__invoke($childSchema, $context)
                    )
                ])
            );
        });
    }
}
