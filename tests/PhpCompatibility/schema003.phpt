--TEST--
SOAP XML Schema 3: simpleType/restriction (reference to type, that is not defined yet)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <restriction base="tns:testType2"/>
    </simpleType>
    <simpleType name="testType2">
        <restriction base="xsd:int"/>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType extends testType2
  > http://test-uri/:testType2 extends integer
