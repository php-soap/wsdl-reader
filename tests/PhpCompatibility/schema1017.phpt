--TEST--
SOAP XML Schema 1017: Inline schemas referencing each other without explicit imports
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="Vehicle" type="string"/>
    <complexType name="testType" xmlns:other="http://test-uri/other/">
        <sequence>
            <element name="selection" type="other:Selection"/>
        </sequence>
    </complexType>
    </schema>
    <schema xmlns="http://www.w3.org/2001/XMLSchema" xmlns:ns1="http://test-uri/" targetNamespace="http://test-uri/other/">
    <complexType name="Selection">
        <sequence>
            <element ref="ns1:Vehicle"/>
        </sequence>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType {
    Selection $selection
  }
  > http://test-uri/:Vehicle extends anySimpleType
  > http://test-uri/other/:Selection {
    string $Vehicle
  }
