#!/usr/bin/env php
<?php

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Formatter\MethodFormatter;
use Soap\WsdlReader\Formatter\ShortTypeFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

require_once dirname(__DIR__).'/vendor/autoload.php';

(static function (array $argv) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    echo "Reading WSDL $file".PHP_EOL;
    $wsdl = (new Wsdl1Reader(new FlatteningLoader(new StreamWrapperLoader())))($file);
    $metadataProvider = new Wsdl1MetadataProvider($wsdl);
    $metadata = $metadataProvider->getMetadata();
    echo PHP_EOL;

    echo "Methods:".PHP_EOL;
    echo implode(PHP_EOL, $metadata->getMethods()->map(fn (Method $method) => '  > '.(new MethodFormatter())($method)));
    echo PHP_EOL.PHP_EOL;

    echo "Types:".PHP_EOL;
    echo implode(PHP_EOL, $metadata->getTypes()->map(fn (Type $type) => '  > '.(new ShortTypeFormatter())($type)));
    echo PHP_EOL.PHP_EOL;
})($argv);
