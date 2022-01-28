#!/usr/bin/env php
<?php

require_once dirname(__DIR__).'/vendor/autoload.php';

(static function (array $argv) {
    if (!$file = $argv[1] ?? null) {
        throw new InvalidArgumentException('Expected wsdl file as first argument');
    }

    $soapCLient = new \SoapClient($file);

    dump(
        $soapCLient->__getFunctions(),
        $soapCLient->__getTypes(),
    );

})($argv);
