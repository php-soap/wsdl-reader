<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use Soap\WsdlReader\Parser\Xml\QnameParser;
use function Psl\Option\none;
use function Psl\Option\some;

class PortTypes
{
    /**
     * @var list<PortType>
     */
    public readonly array $items;

    public function __construct(
        PortType ... $items
    ){
        $this->items = $items;
    }

    /**
     * @return Option<Binding>
     */
    public function lookupByQName(string $qname): Option
    {
        [$namespace, $name] = (new QnameParser())($qname);
        foreach ($this->items as $portType) {
            if ($portType->name === $name) {
                return some($portType);
            }
        }

        return none();
    }
}
