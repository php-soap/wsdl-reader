--TEST--
SOAP XML Schema 69: Attribute with default value (reference)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <attribute name="str" type="string"/>
        <attribute ref="tns:int"/>
    </complexType>
    <attribute name="int" type="int" default="5"/>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    @?string $str
    @?int $int
  }
