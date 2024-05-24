<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Model\Definitions;

enum BindingStyle: string
{
    case DOCUMENT = 'document';
    case RPC = 'rpc';

    public static function tryFromCaseInsensitive(string $value): ?self
    {
        return self::tryFrom(mb_strtolower($value));
    }
}
