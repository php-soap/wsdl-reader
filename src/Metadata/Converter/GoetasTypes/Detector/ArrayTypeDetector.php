<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\GoetasTypes\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexTypeSimpleContent;
use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

final class ArrayTypeDetector
{
    /**
     * @return Option<SimpleType>
     */
    public function __invoke(Type $type): Option
    {
        if ($type instanceof SimpleType) {
            return $this->trySimpleType($type);
        }

        if ($type instanceof ComplexTypeSimpleContent) {
            return $this->tryComplexTypeWithSimpleContent($type);
        }

        return none();
    }

    /**
     * @return Option<SimpleType>
     */
    private function trySimpleType(SimpleType $type): Option
    {
        if ($type->getList()) {
            return some($type->getList());
        }
        if (($rst = $type->getRestriction()) && $rst->getBase() instanceof SimpleType) {
            return $this->__invoke($rst->getBase());
        }

        return none();
    }

    /**
     * @return Option<SimpleType>
     */
    private function tryComplexTypeWithSimpleContent(ComplexTypeSimpleContent $type): Option
    {
        if ($ext = $type->getExtension()) {
            return $this->__invoke($ext->getBase());
        }

        return none();
    }
}
