<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

final class Messages
{
    /**
     * @var list<Message>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        Message ... $items
    ) {
        $this->items = $items;
    }

    /**
     * @return Option<Message>
     */
    public function lookupByName(string $name): Option
    {
        foreach ($this->items as $message) {
            if ($message->name === $name) {
                return some($message);
            }
        }

        return none();
    }
}
