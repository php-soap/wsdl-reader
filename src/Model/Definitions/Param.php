<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

/**
 * tParam in wsdl XSD declaration.
 */
final class Param
{
    public function __construct(
        public readonly string $name,
        public readonly QNamed $message,
    ) {
    }
}
