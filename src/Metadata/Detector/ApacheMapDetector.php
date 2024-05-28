<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Detector;

final class ApacheMapDetector
{
    public const NAMESPACE = 'http://xml.apache.org/xml-soap';

    public static function detect(string $namespace, string $localName): bool
    {
        return mb_strtolower($localName) === 'map' && $namespace === self::NAMESPACE;
    }
}
