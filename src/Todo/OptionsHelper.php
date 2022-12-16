<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Todo;

use Psl\Option\Option;

/**
 * TODO : Use from PSL once available
 */
class OptionsHelper
{

    /**
     * TODO: Once 2.4 is released...
     *
     * Create an option from a mixed value (Some) or null (None).
     *
     * @template T
     *
     * @param null|T $value
     *
     * @return Option<T>
     */
    public static function fromNullable(mixed $value): Option
    {
        return $value !== null ? Option::some($value) : Option::none();
    }

    /**
     * TODO: https://github.com/azjezz/psl/issues/379
     *
     * @template T
     * @template O
     *
     * @param Option<T> $option
     * @param callable(T): O $then
     * @return Option<O>
     */
    public static function andThen(Option $option, callable $then): Option
    {
        if (!$option->isSome()) {
            return $option;
        }

        return $then($option->unwrap());
    }
}