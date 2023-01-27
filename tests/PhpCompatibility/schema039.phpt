--TEST--
SOAP XML Schema 39: Structure with attributes (attributeGroup)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="str" type="string"/>
        </sequence>
        <attributeGroup ref="tns:intGroup"/>
    </complexType>
    <attributeGroup name="intGroup">
        <attribute name="int" type="int"/>
    </attributeGroup>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    string $_
    @int $int
  }
