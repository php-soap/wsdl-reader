<?php

declare(strict_types=1);

namespace Soap\XmlReader\Test\Unit\Xml\Paths;

use PHPUnit\Framework\TestCase;
use Soap\WsdlReader\Xml\Paths\IncludePathBuilder;

class IncludePathBuilderTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideBuildPaths
     */
    public function it_can_build_include_paths(string $relativePath, string $fromFile, string $expected): void
    {
        self::assertSame($expected, IncludePathBuilder::build($relativePath, $fromFile));
    }

    public function provideBuildPaths()
    {
        yield 'same-dir-file' => [
            'relativePath' => 'otherfile.xml',
            'fromFile' => 'somedir/somefile.xml',
            'expected' => 'somedir/otherfile.xml',
        ];
        yield 'child-dir-file' => [
            'relativePath' => '../otherfile.xml',
            'fromFile' => 'somedir/child/somefile.xml',
            'expected' => 'somedir/otherfile.xml',
        ];
        yield 'http-file' => [
            'relativePath' => 'otherfile.xml',
            'fromFile' => 'http://localhost/somedir/somefile.xml',
            'expected' => 'http://localhost/somedir/otherfile.xml',
        ];
        yield 'http-dir-file' => [
            'relativePath' => '../otherfile.xml',
            'fromFile' => 'http://localhost/somedir/child/somefile.xml',
            'expected' => 'http://localhost/somedir/otherfile.xml',
        ];

    }
}
