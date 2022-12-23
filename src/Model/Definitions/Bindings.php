<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use Soap\WsdlReader\Parser\Xml\QnameParser;
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
    public function lookupByQName(string $qname): Option
    {
        [$namespace, $name] = (new QnameParser())($qname);
        foreach ($this->items as $binding) {
            if ($binding->name === $name) {
                return some($binding);
            }
        }

        return none();
    }
}
