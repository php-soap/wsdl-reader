<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\Choice;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementContainer;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\Group;
use GoetasWebservices\XML\XSDReader\Schema\Element\Sequence;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\XsdType as EngineType;
use Soap\WsdlReader\Metadata\Converter\Types\Configurator;
use Soap\WsdlReader\Metadata\Converter\Types\Detector\ElementTypeNameDetector;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Fun\pipe;
use function Psl\Vec\flat_map;

final class ElementContainerVisitor
{
    public function __invoke(ElementContainer $container, TypesConverterContext $context): PropertyCollection
    {
        return new PropertyCollection(
            ...flat_map(
                $container->getElements(),
                fn (ElementItem $element): PropertyCollection => $this->parseElementItem($element, $context)
            )
        );
    }

    private function parseElementItem(ElementItem $element, TypesConverterContext $context): PropertyCollection
    {
        if ($element instanceof Group || $element instanceof Choice || $element instanceof Sequence) {
            return $this->__invoke($element, $context);
        }

        $typeName = (new ElementTypeNameDetector())($element, $context->parent()->unwrap());
        $configure = pipe(
            static fn (EngineType $engineType): EngineType => (new Configurator\ElementConfigurator())($engineType, $element, $context),
            static fn (EngineType $engineType): EngineType => (new Configurator\AnyElementConfigurator())($engineType, $element, $context),
        );

        return new PropertyCollection(
            new Property(
                $element->getName(),
                $configure(EngineType::guess($typeName))
            )
        );
    }
}
