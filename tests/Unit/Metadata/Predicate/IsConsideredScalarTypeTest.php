<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Metadata\Predicate;

use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Model\TypeMeta;
use Soap\WsdlReader\Metadata\Predicate\IsConsideredScalarType;

final class IsConsideredScalarTypeTest extends TestCase
{
    /**
     * @dataProvider provideTests
     *
     */
    public function test_it_knows_if_a_type_is_considered_scalar(TypeMeta $meta, bool $expected): void
    {
        static::assertSame($expected, (new IsConsideredScalarType())($meta));
    }

    public static function provideTests()
    {
        yield 'not' => [
            (new TypeMeta()),
            false,
        ];
        yield 'simple' => [
            (new TypeMeta())->withIsSimple(true),
            true,
        ];
        yield 'attribute' => [
            (new TypeMeta())->withIsSimple(true),
            true,
        ];
    }
}
