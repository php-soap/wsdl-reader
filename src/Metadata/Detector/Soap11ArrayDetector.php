<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Detector;

use Soap\WsdlReader\Model\Definitions\EncodingStyle;

final class Soap11ArrayDetector
{
    public static function detect(string $namespace, string $localName): bool
    {
        return mb_strtolower($localName) === 'array' && $namespace === EncodingStyle::SOAP_11->value;
    }
}
