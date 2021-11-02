#!/usr/bin/env php
<?php

use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\Wsdl\Xml\Validator;
use Soap\WsdlReader\Loader\DebuggingLoader;
use Soap\WsdlReader\WsdlReader;
use VeeWee\Xml\Dom\Document;

require_once dirname(__DIR__).'/vendor/autoload.php';

(static function (array $argv) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    $loader = FlatteningLoader::createForLoader(
        new DebuggingLoader(
            new StreamWrapperLoader()
        )
    );
    $wsdl = Document::fromXmlString($loader($file));

    // file_put_contents('bigass.wsdl', $wsdl->toXmlString());

    echo "Full WSDL:".PHP_EOL;
    echo $wsdl->toXmlString().PHP_EOL.PHP_EOL;

    echo "Validating WSDL:".PHP_EOL;
    $issues = $wsdl->validate(new Validator\WsdlSyntaxValidator());
    echo ($issues->toString() ?: '🟢 ALL GOOD').PHP_EOL.PHP_EOL;

    echo "Validating Schemas".PHP_EOL;
    echo ($wsdl->validate(new Validator\SchemaSyntaxValidator())->toString() ?: '🟢 ALL GOOD').PHP_EOL.PHP_EOL;

    echo "Reading WSDL".PHP_EOL;
    $metadata = WsdlReader::fromLoader($loader)->read($file);

    echo "Methods:".PHP_EOL;
    dump($metadata->getMethods());

    echo "Types:".PHP_EOL;
    dump($metadata->getTypes());

})($argv);
