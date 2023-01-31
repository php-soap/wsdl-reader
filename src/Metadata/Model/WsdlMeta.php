<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Model;

use Psl\Type\TypeInterface;
use function Psl\Type\bool;
use function Psl\Type\int;
use function Psl\Type\mixed_dict;
use function Psl\Type\non_empty_string;
use function Psl\Type\nullable;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\string;
use function Psl\Type\vec;

/**
 * @psalm-type WsdlMetaShape = array{
 *   abstract ?: bool,
 *   default ?: string|null,
 *   docs ?: string,
 *   enums ?: list<array{type: non-empty-string, namespace: non-empty-string, isList: bool}>,
 *   extends ?: array{type: non-empty-string, namespace: non-empty-string},
 *   fixed ?: null|string,
 *   isAlias ?: bool,
 *   isAttribute ?: bool,
 *   isElementValue ?: bool,
 *   isList ?: bool,
 *   isNullable ?: bool,
 *   local ?: bool,
 *   nil ?: bool,
 *   min ?: int,
 *   max ?: int,
 *   restriction ?: array<array-key, mixed>,
 *   unions ?: list<non-empty-string>,
 *   use ?: null|string,
 *   qualified ?: bool,
 * }
 */
final class WsdlMeta
{
    /**
     * @return WsdlMetaShape
     */
    public static function tryParse(mixed $data): array
    {
        return self::type()->coerce($data);
    }

    /**
     * @return TypeInterface<WsdlMetaShape>
     */
    public static function type(): TypeInterface
    {
        return shape([
            'abstract' => optional(bool()),
            'default' => optional(nullable(string())),
            'docs' => optional(string()),
            'enums' => optional(vec(shape([
                'type' => non_empty_string(),
                'namespace' => non_empty_string(),
                'isList' => bool(),
            ], true))),
            'extends' => optional(shape([
                'type' => non_empty_string(),
                'namespace' => non_empty_string(),
            ], true)),
            'fixed' => optional(nullable(string())),
            'isAlias' => optional(bool()), // Simple union/list type
            'isAttribute' => optional(bool()), // Indicates the xsd type is an xml attribute.
            'isElementValue' => optional(bool()), // Indicates the special _ value for attribute groups
            'isList' => optional(bool()), // Simple list or based on min/max occurrences
            'isNullable' => optional(bool()), // Nil or based on min/max occurrences
            'local' => optional(bool()),
            'nil' => optional(bool()),
            'min' => optional(int()),
            'max' => optional(int()),
            'restriction' => optional(mixed_dict()),
            'unions' => optional(vec(non_empty_string())),
            'use' => optional(nullable(string())),
            'qualified' => optional(bool()),
        ], true);
    }
}
