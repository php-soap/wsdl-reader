<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum BindingUse: string
{
    case LITERAL = 'literal';
    case ENCODED = 'encoded';

    public static function tryFromCaseInsensitive(string $value): ?self
    {
        return self::tryFrom(mb_strtolower($value));
    }
}
