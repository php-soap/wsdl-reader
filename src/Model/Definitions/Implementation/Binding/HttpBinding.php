<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Binding;

final class HttpBinding implements BindingImplementation
{
    public function __construct(
        public readonly string $verb,
    ) {
    }
}
