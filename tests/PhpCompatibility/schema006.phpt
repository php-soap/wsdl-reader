--TEST--
SOAP XML Schema 6: simpleType/restriction (referenced by ellement)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <restriction>
            <simpleType name="testType2">
            <restriction base="xsd:int"/>
        </simpleType>
      </restriction>
    </simpleType>
    <element name="testElement" type="tns:testType"/>
EOF;
test_schema($schema,'element="tns:testElement"',123.5);
?>
--EXPECT--
Methods:
  > test(testElement $testParam): void

Types:
  > http://test-uri/:testType
  > http://test-uri/:testType2
  > http://test-uri/:testElement
