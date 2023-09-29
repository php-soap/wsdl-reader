--TEST--
SOAP XML Schema 74: Structure with attributes and qualified elements
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="str" type="string"/>
        </sequence>
        <attribute name="int" type="int"/>
    </complexType>
EOF;

test_schema($schema,'type="tns:testType"', "rpc", "encoded", 'attributeFormDefault="qualified"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    string $str
    @?int $int
  }
