<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Formatter;

use Soap\Engine\Metadata\Model\Type;
use function Psl\Str\format;

final class ShortTypeFormatter
{
    /**
     * TODO: level
     */
    public function __invoke(Type $type): string
    {
        return format(
            '%s:%s',
            $type->getXsdType()->getXmlNamespace(),
            $type->getName(),
        );
    }
}
