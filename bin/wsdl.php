#!/usr/bin/env php
<?php

use Soap\WsdlReader\Loader\LocalFileLoader;
use Soap\WsdlReader\Xml\Parser;
use Soap\WsdlReader\Xml\Validator;

require_once dirname(__DIR__).'/vendor/autoload.php';
$validators = dirname(__DIR__).'/validators';

(static function (array $argv) use ($validators) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    $parser = new Parser(new LocalFileLoader());
    $wsdl = $parser->parse($file);

    echo "Full WSDL:".PHP_EOL;
    echo $wsdl->toXmlString().PHP_EOL.PHP_EOL;

    echo "Validating WSDL:".PHP_EOL;
    $issues = $wsdl->validate(new Validator\WsdlSyntaxValidator());
    echo ($issues->toString() ?: '🟢 ALL GOOD').PHP_EOL.PHP_EOL;

    echo "Validating Schemas".PHP_EOL;
    echo ($wsdl->validate(new Validator\SchemaSyntaxValidator())->toString() ?: '🟢 ALL GOOD').PHP_EOL.PHP_EOL;

})($argv);
