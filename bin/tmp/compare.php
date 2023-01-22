#!/usr/bin/env php
<?php

use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Type;
use Soap\ExtSoapEngine\AbusedClient;
use Soap\ExtSoapEngine\ExtSoapMetadata;
use Soap\ExtSoapEngine\ExtSoapOptions;
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
    $extSoap = AbusedClient::createFromOptions(ExtSoapOptions::defaults($file));
    $extMeta = new ExtSoapMetadata($extSoap);
    $extCount = count($extMeta->getTypes());


    $wsdl = (new Wsdl1Reader(new FlatteningLoader(new StreamWrapperLoader())))($file);
    $metadataProvider = new Wsdl1MetadataProvider($wsdl);
    $metadata = $metadataProvider->getMetadata();

    $metaCount = count($metadata->getTypes());


    var_dump($extCount, $metaCount);



    $ext = $extMeta->getTypes()->map(fn (Type $type) => $type->getName());
    $vanilla = $metadata->getTypes()->map(fn (Type $type) => $type->getName());

    sort($ext);
    sort($vanilla);

    $mapped = array_map(
        fn ($v, $e) => [$v, $e],
        $vanilla,
        $ext,
    );


    var_dump(array_intersect_key($ext, $vanilla));
    var_dump(array_diff($ext, $vanilla));
    var_dump(array_diff($vanilla, $ext));


})($argv);
