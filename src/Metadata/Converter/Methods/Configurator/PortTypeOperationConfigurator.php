<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Definitions\Operation;

final class PortTypeOperationConfigurator
{
    public function __invoke(Method $method, Operation $operation): Method
    {
        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta
            ->withDocs($operation->documentation)
            ->withIsOneWay($operation->isOneWay())
        );
    }
}
