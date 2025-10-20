<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Metadata\Predicate;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\Engine\Metadata\Model\XsdType;
use Soap\WsdlReader\Metadata\Predicate\IsOfType;

final class IsOfTypeTest extends TestCase
{

    #[DataProvider('provideTests')]
    public function test_it_knows_if_a_type_is_considered_nullable(
        string $namespace,
        string $name,
        XsdType $type,
        bool $expected
    ): void {
        static::assertSame($expected, (new IsOfType($namespace, $name))($type));
    }

    public static function provideTests()
    {
        yield 'empty' => [
            'https://test',
            'test',
            (new XsdType('')),
            false,
        ];
        yield 'invalid-type' => [
            'https://test',
            'test',
            (new XsdType('test'))
                ->withXmlTypeName('invalid')
                ->withXmlNamespace('https://test'),
            false,
        ];
        yield 'invalid-ns' => [
            'https://test',
            'test',
            (new XsdType('test'))
                ->withXmlTypeName('test')
                ->withXmlNamespace('invalid'),
            false,
        ];
        yield 'valid' => [
            'https://test',
            'test',
            (new XsdType('test'))
                ->withXmlTypeName('test')
                ->withXmlNamespace('https://test'),
            true,
        ];
        yield 'valid-case-insensitive' => [
            'https://TEST',
            'TEST',
            (new XsdType('test'))
                ->withXmlTypeName('test')
                ->withXmlNamespace('https://test'),
            true,
        ];
        yield 'invalid-extend-type' => [
            'https://test',
            'test',
            (new XsdType(''))
                ->withMeta(
                    static fn (TypeMeta $meta) => $meta->withExtends([
                        'type' => 'invalid',
                        'namespace' => 'https://test',
                        'isSimple' => false,
                    ])
                ),
            false,
        ];
        yield 'invalid-extend-ns' => [
            'https://test',
            'test',
            (new XsdType(''))
                ->withMeta(
                    static fn (TypeMeta $meta) => $meta->withExtends([
                        'type' => 'test',
                        'namespace' => 'invalid',
                        'isSimple' => false,
                    ])
                ),
            false,
        ];
        yield 'valid-extend' => [
            'https://test',
            'test',
            (new XsdType(''))
                ->withMeta(
                    static fn (TypeMeta $meta) => $meta->withExtends([
                        'type' => 'test',
                        'namespace' => 'https://test',
                        'isSimple' => false,
                    ])
                ),
            true,
        ];
        yield 'valid-extend-case-insensitive' => [
            'https://TEST',
            'TEST',
            (new XsdType(''))
                ->withMeta(
                    static fn (TypeMeta $meta) => $meta->withExtends([
                        'type' => 'test',
                        'namespace' => 'https://test',
                        'isSimple' => false,
                    ])
                ),
            true,
        ];
    }
}
