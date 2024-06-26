--TEST--
SOAP XML Schema 72: SOAP 1.1 Array (document style, element with inline type)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="testElement">
    <complexType name="testType">
        <complexContent>
            <restriction base="SOAP-ENC:Array">
        <attribute ref="SOAP-ENC:arrayType" wsdl:arrayType="int[]"/>
        </restriction>
    </complexContent>
    </complexType>
    </element>
EOF;
test_schema($schema,'element="tns:testElement"','document','literal');
?>
--EXPECT--
Methods:
  > test(testElement $testParam): void

Types:
  > http://test-uri/:testType extends Array = (list<int>)
  > http://test-uri/:testElement extends Array = (list<int>)
