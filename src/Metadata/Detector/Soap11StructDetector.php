<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Detector;

use Soap\WsdlReader\Model\Definitions\EncodingStyle;

final class Soap11StructDetector
{
    public static function detect(string $namespace, string $localName): bool
    {
        return mb_strtolower($localName) === 'struct' && $namespace === EncodingStyle::SOAP_11->value;
    }
}
