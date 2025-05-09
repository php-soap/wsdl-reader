<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator\Binding;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\HttpBinding;

final class HttpBindingConfigurator
{
    public function __invoke(Method $method, Binding $binding): Method
    {
        $implementation = $binding->implementation;
        if (!$implementation instanceof HttpBinding) {
            return $method;
        }

        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta
                ->withTransport($implementation->transport->value)
        );
    }
}
