<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\Detector;

use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeContainer;
use GoetasWebservices\XML\XSDReader\Schema\Attribute\AttributeItem;
use GoetasWebservices\XML\XSDReader\Schema\Item;
use GoetasWebservices\XML\XSDReader\Schema\SchemaItem;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

final class AttributeDeclaringParentTypeDetector
{
    /**
     * This class detects the declaring parent type of an attribute.
     * It can be used together with the ParentContext and works as followed
     *
     * - If the parent is an AttributeContainer, it will check if the parent has the attribute
     * - If the parent is not declaring the attribute, it will check if the parent is extending another type and test this extended type.
     *
     * @return Option<Type>
     */
    public function __invoke(AttributeItem $item, ?SchemaItem $parent): Option
    {
        $parent = match(true) {
            $parent instanceof Item => $parent->getType(),
            default => $parent,
        };

        if (!$parent instanceof Type) {
            return none();
        }

        if ($parent instanceof AttributeContainer) {
            foreach ($parent->getAttributes() as $parentAttribute) {
                if ($parentAttribute->getName() === $item->getName()) {
                    /** @var Option<Type> */
                    return some($parent);
                }
            }
        }

        $extensionBase = $parent->getExtension()?->getBase();
        if ($extensionBase) {
            return $this->__invoke($item, $extensionBase);
        }

        return none();
    }
}
