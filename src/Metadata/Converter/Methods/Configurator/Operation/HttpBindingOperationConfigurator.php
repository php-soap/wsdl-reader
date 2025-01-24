<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator\Operation;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Definitions\BindingOperation;
use Soap\WsdlReader\Model\Definitions\Implementation\Operation\HttpOperation;

final class HttpBindingOperationConfigurator
{
    public function __invoke(Method $method, BindingOperation $operation): Method
    {
        $implementation = $operation->implementation;
        if (!$implementation instanceof HttpOperation) {
            return $method;
        }

        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta
                ->withOperationName($operation->name)
        );
    }
}
