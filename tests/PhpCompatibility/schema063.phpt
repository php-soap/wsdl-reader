--TEST--
SOAP XML Schema 63: standard unsignedLong type
--SKIPIF--
<?php exit('skip: method parser cannot deal with base types yet'); ?>
--INI--
precision=14
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = '';
test_schema($schema,'type="xsd:unsignedLong"',0xffffffff);
?>
--EXPECTF--
Methods:
  > test(unsignedLong $testParam): void

Types:
