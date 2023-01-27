<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Str\format;
use function Psl\Vec\filter;

final class LongTypeFormatter
{
    public function __invoke(Type $type): string
    {
        $hasProps = (bool) $type->getProperties()->count();
        $declaration = [
            $type->getXsdType()->getXmlNamespace() . ':'.$type->getName(),
            $type->getXsdType()->getBaseType() ? ' extends '.$type->getXsdType()->getBaseType() : '', // TODO : FQN Base type?
            $hasProps ? ' {' : '',
        ];

        $parts = [
            join('', $declaration),
            ...$type->getProperties()->map(
                static fn (Property $property): string => format(
                    '    %s%s %s%s',
                    ($property->getType()->getMeta()['isAttribute'] ?? false) ? '@' : '',
                    (new XsdTypeFormatter())($property->getType()),
                    '$',
                    $property->getName()
                )
            ),
            $hasProps ? '  }' : '',
        ];

        return join(PHP_EOL, filter($parts));
    }
}
