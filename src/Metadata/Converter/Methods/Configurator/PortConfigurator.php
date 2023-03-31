<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Definitions\Port;

final class PortConfigurator
{
    public function __invoke(Method $method, Port $port): Method
    {
        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta->withLocation($port->address->location)
        );
    }
}
