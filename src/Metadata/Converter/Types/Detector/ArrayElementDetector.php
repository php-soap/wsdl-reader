<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

class ArrayElementDetector
{
    /**
     * @param Type $type
     * @return Option<ElementSingle>
     */
    public function __invoke(mixed $element): Option
    {
        if (!$element instanceof ElementSingle) {
            return none();
        }

        if ($element->getMax() !== -1 || $element->getMax() < 1) {
            return none();
        }

        return some($element);
    }
}
