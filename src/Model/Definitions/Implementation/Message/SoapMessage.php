<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions\Implementation\Message;

use Soap\WsdlReader\Model\Definitions\BindingUse;
use Soap\WsdlReader\Model\Definitions\EncodingStyle;
use VeeWee\Xml\Xmlns\Xmlns;

final class SoapMessage implements MessageImplementation
{
    public function __construct(
        public readonly BindingUse $bindingUse,
        public readonly ?Xmlns $namespace,
        public readonly ?EncodingStyle $encodingStyle
    ) {
    }
}
