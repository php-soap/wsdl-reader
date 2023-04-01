<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

final class Operations
{
    /**
     * @var list<Operation>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        Operation ... $items
    ) {
        $this->items = $items;
    }

    /**
     * @return Option<Operation>
     */
    public function lookupByName(string $name): Option
    {
        foreach ($this->items as $operation) {
            if ($operation->name === $name) {
                return some($operation);
            }
        }

        return none();
    }
}
