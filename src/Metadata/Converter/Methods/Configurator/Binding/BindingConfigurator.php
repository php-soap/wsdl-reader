<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator\Binding;

use Soap\Engine\Metadata\Model\Method;
use Soap\WsdlReader\Model\Definitions\Binding;
use function Psl\Fun\pipe;

final class BindingConfigurator
{
    public function __invoke(Method $method, Binding $binding): Method
    {
        return pipe(
            static fn (Method $method) => (new HttpBindingConfigurator())($method, $binding),
            static fn (Method $method) => (new SoapBindingConfigurator())($method, $binding),
        )($method);
    }
}
