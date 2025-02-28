<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

enum InformationType : string
{
    case Meta = 'meta info';
    case Type = 'type info';

    public function toggle(): self
    {
        return match ($this) {
            InformationType::Meta => InformationType::Type,
            InformationType::Type => InformationType::Meta,
        };
    }
}
