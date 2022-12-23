--TEST--
SOAP XML Schema 63: standard unsignedLong type
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
