--TEST--
SOAP XML Schema 36: Nested complex types (inline)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="int" type="int"/>
            <element name="testType2">
                <complexType>
                    <sequence>
                        <element name="int" type="int"/>
                    </sequence>
                </complexType>
            </element>
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
