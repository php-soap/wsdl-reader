<?php declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator\Operation;

use Soap\Engine\Metadata\Model\Method;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use function Psl\Fun\pipe;

final readonly class OperationConfigurator
{
    public function __invoke(Method $method, BindingOperation $operation): Method
    {
        return pipe(
            static fn (Method $method) => (new HttpBindingOperationConfigurator())($method, $operation),
            static fn (Method $method) => (new SoapBindingOperationConfigurator())($method, $operation),
        )($method);
    }
}
