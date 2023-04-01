<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Implementation\Binding\SoapBinding;

final class BindingConfigurator
{
    public function __invoke(Method $method, Binding $binding): Method
    {
        $implementation = $binding->implementation;
        if (!$implementation instanceof SoapBinding) {
            return $method;
        }

        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta
            ->withTransport($implementation->transport->value)
        );
    }
}
