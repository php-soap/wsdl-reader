--TEST--
SOAP XML Schema 35: Nested complex types (element ref + anonymous type)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="testType2">
        <complexType>
            <sequence>
                <element name="int" type="int"/>
            </sequence>
        </complexType>
    </element>
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
  > http://test-uri/:testType
  > http://test-uri/:testType2
