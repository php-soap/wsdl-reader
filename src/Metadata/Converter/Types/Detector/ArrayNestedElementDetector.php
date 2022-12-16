<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Psl\Option\Option;
use function Psl\Iter\first;
use function Psl\Option\none;
use function Psl\Option\some;

class ArrayNestedElementDetector
{
    /**
     * @return Option<ElementSingle>
     */
    public function __invoke(Type $type): Option
    {
        if (!$type instanceof ComplexType) {
            return none();
        }

        if ($type->getParent()) {
            return none();
        }

        if ($type->getAttributes()) {
            return none();
        }

        $elements = $type->getElements();
        if (count($elements) !== 1) {
            return none();
        }

        $element = first($elements) ? some(first($elements)) : none(); // TODO use new option factory instead ;)
        return $element->map(
            fn (mixed $element) => (new ArrayElementDetector())($element)
        );
    }
}
