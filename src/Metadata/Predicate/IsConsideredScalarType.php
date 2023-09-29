<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Predicate;

use Soap\Engine\Metadata\Model\TypeMeta;

final class IsConsideredScalarType
{
    public function __invoke(TypeMeta $meta): bool
    {
        return $meta->isSimple()->unwrapOr(false)
            || $meta->isAttribute()->unwrapOr(false);
    }
}
