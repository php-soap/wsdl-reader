#!/usr/bin/env php
<?php

use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

require_once dirname(__DIR__).'/vendor/autoload.php';

(static function (array $argv) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    echo "Reading WSDL".PHP_EOL;
    $wsdl = (new Wsdl1Reader(new StreamWrapperLoader()))($file);
    /*var_dump(
        $wsdl->bindings->items,
        $wsdl->portTypes->items,
        $wsdl->messages->items,
        $wsdl->services->items,
        //$wsdl->schema
    );*/
    $metadataProvider = new Wsdl1MetadataProvider($wsdl);
    $metadata = $metadataProvider->getMetadata();

    echo "Methods:".PHP_EOL;
    dump($metadata->getMethods());

    echo "Types:".PHP_EOL;
    dump($metadata->getTypes());
})($argv);
