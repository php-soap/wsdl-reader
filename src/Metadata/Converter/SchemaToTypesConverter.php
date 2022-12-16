<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementDef;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type as SoapType;
use Soap\WsdlReader\Metadata\Converter\Types\ConverterContext;
use Soap\WsdlReader\Metadata\Converter\Types\Visitor\ElementVisitor;
use Soap\WsdlReader\Metadata\Converter\Types\Visitor\SoapTypeVisitor;
use function Psl\Vec\flat_map;
use function Psl\Vec\map;

// BASE ON
// * https://github.com/goetas-webservices/wsdl2php/tree/master/src/Generation
// * https://github.com/goetas-webservices/xsd2php/blob/master/src/AbstractConverter.php
// * https://github.com/goetas-webservices/xsd2php/blob/master/src/Php/PhpConverter.php
class SchemaToTypesConverter
{
    public function __invoke(Schema $schema, ConverterContext $context): TypeCollection
    {
        return $context->visit($schema, function () use ($schema, $context): TypeCollection {
            return new TypeCollection(
                ...map($schema->getTypes(), fn (Type $type): SoapType => (new SoapTypeVisitor())($type, $context)),
                ...map($schema->getElements(), fn (ElementDef $element): SoapType => (new ElementVisitor())($element, $context)),
                ...flat_map(
                    $schema->getSchemas(),
                    fn (Schema $childSchema): TypeCollection => $this->__invoke($childSchema, $context)
                )
            );
        });
    }
}
