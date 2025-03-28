<?php declare(strict_types=1);

namespace Soap\WsdlReader\Console\UI\Page;

use InvalidArgumentException;

enum WsdlInfo : string
{
    case Bindings = 'Bindings';
    case Messages = 'Messages';
    case PortTypes = 'PortTypes';
    case Services = 'Services';
    case Namespaces = 'Namespaces';

    public function index(): int
    {
        foreach (self::cases() as $index => $case) {
            if ($case === $this) {
                return $index;
            }
        }

        throw new InvalidArgumentException('There should always be a match');
    }

    public static function default(): self
    {
        return self::Services;
    }
}
