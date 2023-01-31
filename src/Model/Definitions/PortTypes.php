<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

final class PortTypes
{
    /**
     * @var list<PortType>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        PortType ... $items
    ) {
        $this->items = $items;
    }

    /**
     * @return Option<PortType>
     */
    public function lookupByName(string $name): Option
    {
        foreach ($this->items as $portType) {
            if ($portType->name === $name) {
                return some($portType);
            }
        }

        return none();
    }
}
