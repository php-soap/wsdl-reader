<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\Property;
use Soap\Engine\Metadata\Model\Type;
use function Psl\Str\format;

class LongTypeFormatter
{
    /**
     * TODO: level
     */
    public function __invoke(Type $type, int $level = 1): string
    {
        return format(
            '{%s:%s}{%s}',
            $type->getXsdType()->getXmlNamespace(),
            $type->getName(),
            join('', $type->getProperties()->map(
                fn (Property $property): string => format(
                    '%s    %s $%s',
                    PHP_EOL,
                    (new XsdTypeFormatter())($property->getType()),
                    $property->getName()
                )
            )).PHP_EOL.'  '
        );
    }
}