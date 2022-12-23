--TEST--
SOAP XML Schema 47: Extension of complex type (2)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType2">
        <sequence>
            <element name="int" type="int"/>
        </sequence>
    </complexType>
    <complexType name="testType">
        <complexContent>
            <extension base="tns:testType2">
                <attribute name="int2" type="int"/>
            </extension>
        </complexContent>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType2
  > http://test-uri/:testType
