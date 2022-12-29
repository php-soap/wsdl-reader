--TEST--
SOAP XML Schema 34: Nested complex types (element ref)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="testType2" type="tns:testType2"/>
    <complexType name="testType2">
        <sequence>
            <element name="int" type="int"/>
        </sequence>
    </complexType>
    <complexType name="testType">
        <sequence>
            <element name="int" type="int"/>
            <element ref="tns:testType2"/>
        </sequence>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType2 {
    int $int
  }
  > http://test-uri/:testType {
    int $int
    testType2 $testType2
  }
