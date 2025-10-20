<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Metadata\Coverter\Types\SoapEnc;

use InvalidArgumentException;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Soap\WsdlReader\Metadata\Converter\Types\SoapEnc\ArrayTypeInfo;

final class ArrayTypeInfoTest extends TestCase
{
    #[DataProvider('provideSoap11ArrayTypes')]
    public function test_it_can_parse_array_type_information_from_soap_11_information(
        string $raw,
        string $expectedPrefix,
        string $expectedType,
        string $expectedRank,
        string $expectedItemType,
        bool $expectedIsMultiDimensional,
        int $expectedMaxOccurs
    ): void {
        $info = ArrayTypeInfo::parseSoap11($raw);

        static::assertSame($expectedPrefix, $info->prefix);
        static::assertSame($expectedType, $info->type);
        static::assertSame($expectedRank, $info->rank);
        static::assertSame($expectedItemType, $info->itemType());
        static::assertSame($expectedIsMultiDimensional, $info->isMultiDimensional());
        static::assertSame($expectedMaxOccurs, $info->getMaxOccurs());
        static::assertSame($raw, $info->toString());
    }

    #[DataProvider('provideSoap12ArrayTypes')]
    public function test_it_can_parse_array_type_information_from_soap_12_information(
        string $itemType,
        string $arraySize,
        string $expectedPrefix,
        string $expectedType,
        string $expectedRank,
        string $expectedItemType,
        bool $expectedIsMultiDimensional,
        int $expectedMaxOccurs,
        string $expectedSoap11Type
    ): void {
        $info = ArrayTypeInfo::parseSoap12($itemType, $arraySize);

        static::assertSame($expectedPrefix, $info->prefix);
        static::assertSame($expectedType, $info->type);
        static::assertSame($expectedRank, $info->rank);
        static::assertSame($expectedItemType, $info->itemType());
        static::assertSame($expectedIsMultiDimensional, $info->isMultiDimensional());
        static::assertSame($expectedMaxOccurs, $info->getMaxOccurs());
        static::assertSame($expectedSoap11Type, $info->toString());
    }


    public function test_it_invalid_format(): void
    {
        $this->expectException(InvalidArgumentException::class);

        ArrayTypeInfo::parseSoap11('int');
    }

    public static function provideSoap11ArrayTypes(): iterable
    {
        yield 'regular array' => [
            'raw' => 'int[]',
            'expectedPrefix' => '',
            'expectedType' => 'int',
            'expectedRank' => '[]',
            'expectedItemType' => 'int',
            'expectedIsMultiDimensional' => false,
            'expectedMaxOccurs' => -1,
        ];

        yield 'prefixed regular array' => [
            'raw' => 'xsd:int[]',
            'expectedPrefix' => 'xsd',
            'expectedType' => 'int',
            'expectedRank' => '[]',
            'expectedItemType' => 'xsd:int',
            'expectedIsMultiDimensional' => false,
            'expectedMaxOccurs' => -1,
        ];

        yield 'sized regular array' => [
            'raw' => 'int[5]',
            'expectedPrefix' => '',
            'expectedType' => 'int',
            'expectedRank' => '[5]',
            'expectedItemType' => 'int',
            'expectedIsMultiDimensional' => false,
            'expectedMaxOccurs' => 5,
        ];

        yield 'multi dimensional array comma syntax' => [
            'raw' => 'int[,][3]',
            'expectedPrefix' => '',
            'expectedType' => 'int',
            'expectedRank' => '[,][3]',
            'expectedItemType' => 'int',
            'expectedIsMultiDimensional' => true,
            'expectedMaxOccurs' => 3,
        ];
    }

    public static function provideSoap12ArrayTypes(): iterable
    {
        yield 'regular array' => [
            'itemType' => 'int',
            'arraySize' => '*',
            'expectedPrefix' => '',
            'expectedType' => 'int',
            'expectedRank' => '[]',
            'expectedItemType' => 'int',
            'expectedIsMultiDimensional' => false,
            'expectedMaxOccurs' => -1,
            'expectedSoap11Type' => 'int[]',
        ];

        yield 'prefixed regular array' => [
            'itemType' => 'xsd:int',
            'arraySize' => '*',
            'expectedPrefix' => 'xsd',
            'expectedType' => 'int',
            'expectedRank' => '[]',
            'expectedItemType' => 'xsd:int',
            'expectedIsMultiDimensional' => false,
            'expectedMaxOccurs' => -1,
            'expectedSoap11Type' => 'xsd:int[]',
        ];

        yield 'sized regular array' => [
            'itemType' => 'int',
            'arraySize' => '5',
            'expectedPrefix' => '',
            'expectedType' => 'int',
            'expectedRank' => '[5]',
            'expectedItemType' => 'int',
            'expectedIsMultiDimensional' => false,
            'expectedMaxOccurs' => 5,
            'expectedSoap11Type' => 'int[5]',
        ];

        yield 'multi dimensional array comma syntax' => [
            'itemType' => 'int',
            'arraySize' => '3 *',
            'expectedPrefix' => '',
            'expectedType' => 'int',
            'expectedRank' => '[,][3]',
            'expectedItemType' => 'int',
            'expectedIsMultiDimensional' => true,
            'expectedMaxOccurs' => 3,
            'expectedSoap11Type' => 'int[,][3]',
        ];
    }
}
