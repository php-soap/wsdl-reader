#!/usr/bin/env php
<?php

use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Metadata\Provider\WsdlReadingMetadataProvider;
use VeeWee\Xml\Dom\Document;

require_once dirname(__DIR__).'/vendor/autoload.php';

(static function (array $argv) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    $loader = new StreamWrapperLoader();
    $wsdl = Document::fromXmlString($loader($file));

    echo "Reading WSDL".PHP_EOL;
    $metadata = (new WsdlReadingMetadataProvider($wsdl))->getMetadata();

    echo "Methods:".PHP_EOL;
    dump($metadata->getMethods());

    echo "Types:".PHP_EOL;
    dump($metadata->getTypes());
})($argv);
