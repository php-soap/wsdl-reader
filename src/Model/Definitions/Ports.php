<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Psl\Option\Option;
use Soap\WsdlReader\Locator\ServiceSelectionCriteria;
use function Psl\Option\none;
use function Psl\Option\some;

final class Ports
{
    /**
     * @var list<Port>
     */
    public readonly array $items;

    /**
     * @no-named-arguments
     */
    public function __construct(
        Port ... $items
    ) {
        $this->items = $items;
    }

    /**
     * Searches for the preferred SOAP version.
     * If there is no preferred version - it takes the first port it finds!
     *
     * @return Option<Port>
     */
    public function lookupByLookupServiceCriteria(ServiceSelectionCriteria $criteria): Option
    {
        $preferredVersion = $criteria->preferredSoapVersion;
        $allowHttp = $criteria->allowHttpPorts;

        foreach ($this->items as $port) {
            if (!$allowHttp && $port->address->type->isHttp()) {
                continue;
            }

            if ($preferredVersion && $port->address->type->soapVersion() !== $preferredVersion) {
                continue;
            }

            return some($port);
        }

        return none();
    }
}
