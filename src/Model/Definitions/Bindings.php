<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

final class Bindings
{
    /**
     * @var list<Binding>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        Binding ... $items
    ) {
        $this->items = $items;
    }

    /**
     * @return Option<Binding>
     */
    public function lookupByName(string $name): Option
    {
        foreach ($this->items as $binding) {
            if ($binding->name === $name) {
                return some($binding);
            }
        }

        return none();
    }
}
