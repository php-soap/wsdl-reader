--TEST--
SOAP XML Schema 5: simpleType/restriction (inline type)
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
EOF;
test_schema($schema,'type="tns:testType"',123.5);
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType extends testType2
  > http://test-uri/:testType2 extends integer
