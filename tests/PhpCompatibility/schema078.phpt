--TEST--
SOAP XML Schema 78: Element form qualified/unqualified (elementFormDefault="qualified")
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <sequence>
            <element name="int1" type="int"/>
            <element name="int2" type="int" form="qualified"/>
            <element name="int3" type="int" form="unqualified"/>
        </sequence>
    </complexType>
EOF;

test_schema($schema,'type="tns:testType"', "rpc", "literal", 'elementFormDefault="qualified"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    int $int1
    int $int2
    int $int3
  }

