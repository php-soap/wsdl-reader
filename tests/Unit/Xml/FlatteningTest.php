<?php

declare(strict_types=1);

namespace Soap\XmlReader\Test\Unit\Xml\Paths;

use PHPUnit\Framework\TestCase;
use Soap\WsdlReader\Loader\Loader;
use Soap\WsdlReader\Loader\LocalFileLoader;
use Soap\WsdlReader\Xml\Parser;

class FlatteningTest extends TestCase
{
    /** @test */
    public function it_can_flatten_simple_wsdl(): void
    {
        $source = FIXTURE_DIR . '/wsdl/imports/wsdl.xml';
        $parser = new Parser(
            new LocalFileLoader()
        );

        $document = $parser->parse($source);

        echo $document->toXmlString();
    }
}
