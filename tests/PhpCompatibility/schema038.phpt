--TEST--
SOAP XML Schema 38: Structure with attributes (ref)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="str" type="string"/>
        </sequence>
        <attribute ref="tns:int"/>
    </complexType>
    <attribute name="int" type="int"/>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    string $str
    @int $int
  }
