--TEST--
SOAP XML Schema 75: Attributes form qualified/unqualified (attributeFormDefault="qualified")
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <attribute name="int1" type="int"/>
        <attribute name="int2" type="int" form="qualified"/>
        <attribute name="int3" type="int" form="unqualified"/>
    </complexType>
EOF;

test_schema($schema,'type="tns:testType"', "rpc", "encoded", 'attributeFormDefault="qualified"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    todo $_
    int $int1
    int $int2
    int $int3
  }
