<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

use Soap\WsdlReader\Parser\Definitions\QNamed;

/**
 * tParam in wsdl XSD declaration.
 */
final class Param
{
    public function __construct(
        public readonly string $name,
        public readonly QNamed $message,
    ){
    }
}
