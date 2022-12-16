<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use function Psl\Option\none;
use function Psl\Option\some;

class Bindings
{
    /**
     * @var list<Binding>
     */
    public readonly array $items;

    public function __construct(
        Binding ... $items
    ){
        $this->items = $items;
    }




    /**
     * Searches for the preferred SOAP version.
     * If there is no preferred version - it takes the first binding it finds!
     *
     * @return Option<Binding>
     */
    public function lookupBySoapVersion(?SoapVersion $preferredVersion): Option
    {
        foreach ($this->items as $binding) {
            if (!$preferredVersion || $binding->soapVersion === $preferredVersion) {
                return some($binding);
            }
        }

        return none();
    }

    /**
     * @return Option<Binding>
     */
    public function lookupByIdentifier(string $type): Option
    {
        foreach ($this->items as $binding) {
            if ($binding->type === $type) {
                return some($binding);
            }
        }

        return none();
    }
}
