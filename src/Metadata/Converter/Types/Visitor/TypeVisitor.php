<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\Engine\Metadata\Model\Type as EngineType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class TypeVisitor
{
    public function __invoke(Type $xsdType, TypesConverterContext $context): TypeCollection
    {
        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new Configurator\TypeConfigurator())($metaType, $xsdType, $context),
        );

        return new TypeCollection(
            new EngineType(
                $configure(MetaType::guess($xsdType->getName() ?? '')),
                (new PropertiesVisitor())($xsdType, $context)
            ),
            ...((new InlineElementTypeVisitor())($xsdType, $context))
        );
    }
}
