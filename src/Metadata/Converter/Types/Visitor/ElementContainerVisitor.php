<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Visitor;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementContainer;
use GoetasWebservices\XML\XSDReader\Schema\Element\ElementItem;
use GoetasWebservices\XML\XSDReader\Schema\Element\GroupRef;
use Soap\Engine\Metadata\Collection\PropertyCollection;
use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Converter\Types\TypesConverterContext;
use function Psl\Vec\flat_map;

final class ElementContainerVisitor
{
    public function __invoke(ElementContainer $container, TypesConverterContext $context): PropertyCollection
    {
        return new PropertyCollection(
            ...flat_map(
                $container->getElements(),
                fn (ElementItem $element): PropertyCollection => $this->parseElementItem($element)
            )
        );
    }

    private function parseElementItem(ElementItem $element): PropertyCollection
    {
        if ($element instanceof GroupRef) {
            // TODO : parse $element->getElements(); ? What with min/max occurences on this type?
            return new PropertyCollection();
        }

        $typeName = $element->getType()?->getName() ?: $element->getName();

        return new PropertyCollection(
            new Property(
                $element->getName(),
                // TODO : Type info...
                (new XsdType($typeName))
                    ->withXmlNamespace($element->getSchema()->getTargetNamespace())
                    ->withXmlNamespaceName('TODO') // TODO
                    ->withMeta([
                        'min' => $element->getMin(),
                        'max' => $element->getMax(),
                        'nil' => $element->isNil(),
                        'default' => $element->getDefault(),
                        'docs' => $element->getDoc(),
                        // 'type' => $element->getType()
                    ])
            )
        );
    }
}
