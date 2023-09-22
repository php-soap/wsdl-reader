--TEST--
SOAP XML Schema 7: simpleType/restriction (referenced by ellement)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="testElement" type="tns:testType"/>
    <simpleType name="testType">
        <restriction>
            <simpleType name="testType2">
            <restriction base="xsd:int"/>
        </simpleType>
      </restriction>
    </simpleType>
EOF;
test_schema($schema,'element="tns:testElement"',123.5);
?>
--EXPECT--
Methods:
  > test(testElement $testParam): void

Types:
  > http://test-uri/:testType extends testType2
  > http://test-uri/:testType2 extends int
  > http://test-uri/:testElement extends testType2
