<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Metadata\Converter\Types\SoapEnc;

use InvalidArgumentException;
use Soap\WsdlReader\Parser\Xml\QnameParser;
use function Psl\Regex\first_match;
use function Psl\Regex\split;
use function Psl\Str\contains;
use function Psl\Type\int;
use function Psl\Type\optional;
use function Psl\Type\shape;
use function Psl\Type\string;
use function Psl\Vec\filter;

/**
 * @see https://www.w3.org/TR/2000/NOTE-SOAP-20000508/#_Toc478383512
 *
 * arrayTypeValue = atype asize
 * atype          = QName *( rank )
 * rank           = "[" *( "," ) "]"
 * asize          = "[" #length "]"
 * length         = 1*DIGIT
 *
 * For example, an array with 5 members of type array of integers would have an arrayTypeValue value of "int[][5]"
 * of which the atype value is "int[]" and the asize value is "[5]".
 *
 * Likewise, an array with 3 members of type two-dimensional arrays of integers would have an arrayTypeValue value of "int[,][3]"
 * of which the atype value is "int[,]" and the asize value is "[3]".
 */
final class ArrayTypeInfo
{
    private const PATTERN_A_RANK = '/\[(?<maxOccurs>\d*)\]$/i';

    public function __construct(
        public readonly string $prefix,
        public readonly string $type,
        public readonly string $rank
    ) {
    }

    public static function parseSoap11(string $type): self
    {
        [$prefix, $arrayType] = (new QnameParser())($type);
        $parts = explode('[', $arrayType, 2);

        if (count($parts) !== 2) {
            throw new InvalidArgumentException(
                'Invalid arrayType given. Expected format: qname[rank], got: "'.$type.'".'
            );
        }

        [$type, $rank] = $parts;

        return new self($prefix, $type, '['.$rank);
    }

    /**
     * @see https://www.w3.org/TR/soap12-part2/#arraySizeattr
     *
     * The type of the arraySize attribute information item is enc:arraySize. The value of the arraySize attribute information item MUST conform to the following EBNF grammar
     * [1]    arraySizeValue       ::=    ("*" | concreteSize) nextConcreteSize*
     * [2]    nextConcreteSize       ::=    whitespace concreteSize
     * [3]    concreteSize       ::=    [0-9]+
     * [4]    white space       ::=    (#x20 | #x9 | #xD | #xA)+
     *
     * Pattern of value: (\*|(\d+))(\s+\d+)*
     *
     * The arraySize attribute conveys a suggested mapping of a SOAP array to a multi-dimensional program data structure. The cardinality of the arraySize list represents the number of dimensions, with individual values providing the extents of the respective dimensions. When SOAP encoding multidimensional arrays, nodes are selected such that the last subscript (i.e., the subscript corresponding to the last specified dimension) varies most rapidly, and so on with the first varying most slowly. An asterisk MAY be used only in place of the first size to indicate a dimension of unspecified extent; asterisks MUST NOT appear in other positions in the list. The default value of the arraySize attribute information item is "*", i.e., a single dimension of unspecified extent.
     */
    public static function parseSoap12(string $itemType, string $arraySize): self
    {
        [$prefix, $type] = (new QnameParser())($itemType);

        $parts = filter(
            split($arraySize, '/\s+/'),
            static fn (string $part): bool => $part !== ''
        );
        $partsCount = count($parts);

        $rank = match ($partsCount) {
            0 => '[]',
            1 => '['.($parts[0] === '*' ? '' : $parts[0]).']',
            default => '[,]['.($parts[0] === '*' ? '-1' : $parts[0]).']',
        };

        return new self($prefix, $type, $rank);
    }

    public function isMultiDimensional(): bool
    {
        return contains($this->rank, ',');
    }

    public function getMaxOccurs(): int
    {
        $parts = first_match($this->rank, self::PATTERN_A_RANK, shape([
            'maxOccurs' => optional(string()),
        ]));

        $maxOccurs = $parts['maxOccurs'] ?? null;
        if (!$maxOccurs) {
            return -1;
        }

        return int()->coerce($maxOccurs);
    }

    public function toString(): string
    {
        return sprintf(
            '%s%s%s',
            $this->prefix ? $this->prefix . ':' : '',
            $this->type,
            $this->rank,
        );
    }

    public function itemType(): string
    {
        return sprintf(
            '%s%s',
            $this->prefix ? $this->prefix . ':' : '',
            $this->type,
        );
    }

    public function withPrefix(string $prefix): self
    {
        return new self($prefix, $this->type, $this->rank);
    }
}
