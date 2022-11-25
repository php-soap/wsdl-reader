#!/usr/bin/env php
<?php

use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Metadata\Provider\WsdlReadingMetadataProvider;
use Soap\WsdlReader\OldStuff\Wsdl1Reader;

require_once dirname(__DIR__).'/vendor/autoload.php';

(static function (array $argv) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    echo "Reading WSDL".PHP_EOL;
    $reader = (new Wsdl1Reader(new StreamWrapperLoader()));
    $metadata = $reader($file);

    echo "Methods:".PHP_EOL;
    dump($metadata->getMethods());

    echo "Types:".PHP_EOL;
    dump($metadata->getTypes());
})($argv);
