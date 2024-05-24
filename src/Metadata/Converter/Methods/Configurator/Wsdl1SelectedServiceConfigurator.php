<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator;

use Soap\Engine\Metadata\Model\Method;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use function Psl\Fun\pipe;

final class Wsdl1SelectedServiceConfigurator
{
    public function __invoke(Method $method, Wsdl1SelectedService $service): Method
    {
        return pipe(
            static fn (Method $method) => (new BindingConfigurator())($method, $service->binding),
            static fn (Method $method) => (new PortConfigurator())($method, $service->port),
            static fn (Method $method) => (new Wsdl1Configurator())($method, $service->wsdl),
        )($method);
    }
}
