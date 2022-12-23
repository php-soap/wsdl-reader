--TEST--
SOAP XML Schema 54: Apache Map
--INI--
precision=14
--FILE----
<?php
include __DIR__."/test_schema.inc";
$schema = '';
test_schema($schema,'type="apache:Map" xmlns:apache="http://xml.apache.org/xml-soap"');
?>
--EXPECT--
Methods:
  > test(Map $testParam): void

Types:
