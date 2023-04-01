<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementDef;
use Soap\Engine\Metadata\Model\Type as EngineType;
use Soap\Engine\Metadata\Model\XsdType as MetaType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;

final class ElementVisitor
{
    public function __invoke(ElementDef $element, TypesConverterContext $context): ?EngineType
    {
        // When there is no type set on the element, we cannot do anything with it here. There should always be one somehow.
        $xsdType = $element->getType();
        if (!$xsdType) {
            return null;
        }

        // On <element name="Categories" type="tns:Categories" />
        // The element proxies to a type.
        // This method will skip the Element -> The complex type will be imported instead!
        if (
            $xsdType->getName() === $element->getName()
            && $xsdType->getSchema()->getTargetNamespace() === $element->getSchema()->getTargetNamespace()
        ) {
            return null;
        }

        $configure = pipe(
            static fn (MetaType $metaType): MetaType => (new Configurator\ElementConfigurator())($metaType, $element, $context),
        );

        return new EngineType(
            $configure(MetaType::guess($element->getName())),
            (new PropertiesVisitor())($xsdType, $context)
        );
    }
}
