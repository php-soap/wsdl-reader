--TEST--
SOAP XML Schema 1006: simple element types
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="AcknowledgeReceipt" type="anyType" />
EOF;
test_schema($schema,'type="tns:AcknowledgeReceipt"');
?>
--EXPECT--
Methods:
  > test(AcknowledgeReceipt $testParam): void

Types:
  > http://test-uri/:AcknowledgeReceipt
