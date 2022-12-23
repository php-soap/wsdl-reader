--TEST--
SOAP XML Schema 41: Structure (group)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <group ref="tns:testGroup"/>
    </complexType>
    <group name="testGroup">
        <sequence>
            <element name="int" type="int"/>
            <element name="str" type="string"/>
        </sequence>
    </group>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType
