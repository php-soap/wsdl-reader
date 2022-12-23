<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Soap\WsdlReader\Parser\Definitions\QNamed;

final class Part
{
    public function __construct(
        public readonly string $name,
        public readonly QNamed $element,
    ){
    }
}
