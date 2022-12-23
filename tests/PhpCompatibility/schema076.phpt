--TEST--
SOAP XML Schema 76: Attributes form qualified/unqualified (attributeFormDefault="unqualified")
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

test_schema($schema,'type="tns:testType"', "rpc", "encoded", 'attributeFormDefault="unqualified"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType
