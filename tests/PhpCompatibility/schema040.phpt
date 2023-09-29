--TEST--
SOAP XML Schema 40: Structure with attributes (inline types)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="str" type="string"/>
        </sequence>
        <attribute name="int">
            <simpleType>
                <restriction base="int"/>
            </simpleType>
        </attribute>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    string $str
    @?int $int
  }
