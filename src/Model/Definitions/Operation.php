<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

class Operation
{
    public function __construct(
        public readonly string $name,
        public readonly string $soapAction,
        public readonly string $style,
        public readonly Param $input,
        public readonly ?Param $output,
    ){
    }
}
