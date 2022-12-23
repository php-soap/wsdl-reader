<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Methods;

use Soap\Engine\Metadata\Collection\TypeCollection;
use Soap\WsdlReader\Model\Definitions\SoapVersion;

class MethodsConverterContext
{
    private function __construct(
        public readonly TypeCollection $types,
        public readonly ?SoapVersion $preferredSoapVersion = null
    ){
    }

    public static function defaults(TypeCollection $types, ?SoapVersion $preferredSoapVersion): self
    {
        return new self($types, $preferredSoapVersion);
    }
}
