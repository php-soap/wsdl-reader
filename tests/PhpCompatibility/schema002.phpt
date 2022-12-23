--TEST--
SOAP XML Schema 2: simpleType/restriction (reference to type)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType2">
        <restriction base="xsd:int"/>
    </simpleType>
    <simpleType name="testType">
        <restriction base="tns:testType2"/>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType2
  > http://test-uri/:testType
