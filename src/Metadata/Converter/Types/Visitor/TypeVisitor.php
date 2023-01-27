<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Type as SoapType;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class TypeVisitor
{
    public function __invoke(Type $xsdType, TypesConverterContext $context): SoapType
    {
        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new Configurator\TypeConfigurator())($metaType, $xsdType, $context),
            static fn (MetaType $metaType): MetaType => (new Configurator\NamespaceConfigurator())($metaType, $xsdType, $context),
        );

        return new SoapType(
            $configure(XsdType::create($xsdType->getName())),
            new PropertyCollection(...(new PropertiesVisitor())($xsdType, $context))
        );
    }
}
