<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementDef;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type as SoapType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use Soap\WsdlReader\Metadata\Converter\Types\Visitor\ElementVisitor;
use Soap\WsdlReader\Metadata\Converter\Types\Visitor\TypeVisitor;
use function Psl\Vec\filter_nulls;
use function Psl\Vec\flat_map;
use function Psl\Vec\map;

// BASE ON
// * https://github.com/goetas-webservices/wsdl2php/tree/master/src/Generation
// * https://github.com/goetas-webservices/xsd2php/blob/master/src/AbstractConverter.php
// * https://github.com/goetas-webservices/xsd2php/blob/master/src/Php/PhpConverter.php
final class SchemaToTypesConverter
{
    public function __invoke(Schema $schema, TypesConverterContext $context): TypeCollection
    {
        return $context->visit($schema, function () use ($schema, $context): TypeCollection {
            return new TypeCollection(
                ...filter_nulls([
                    ...map($schema->getTypes(), static fn (Type $type): SoapType => (new TypeVisitor())($type, $context)),
                    ...map($schema->getElements(), static fn (ElementDef $element): ?SoapType => (new ElementVisitor())($element, $context)),
                    ...flat_map(
                        $schema->getSchemas(),
                        fn (Schema $childSchema): TypeCollection => $this->__invoke($childSchema, $context)
                    )
                ])
            );
        });
    }
}
