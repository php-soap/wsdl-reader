<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods\Configurator;

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\MethodMeta;
use Soap\WsdlReader\Model\Wsdl1;

final class Wsdl1Configurator
{
    public function __invoke(Method $method, Wsdl1 $wsdl): Method
    {
        return $method->withMeta(
            static fn (MethodMeta $meta): MethodMeta => $meta
                ->withTargetNamespace($wsdl->targetNamespace?->value())
        );
    }
}
