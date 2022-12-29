--TEST--
SOAP XML Schema 52: Array in complex type (maxOccurs > 1, empty array)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testTypeWrapper">
        <restriction base="tns:testType"/>
    </simpleType>
    <complexType name="testType">
        <sequence>
            <element name="int1" type="int"/>
            <element name="int2" type="int" minOccurs="0" maxOccurs="unbounded"/>
            <element name="int3" type="int" minOccurs="1" maxOccurs="unbounded"/>
            <element name="int4" type="int" minOccurs="1" maxOccurs="3"/>
            <element name="int5" type="int" minOccurs="0" maxOccurs="1"/>
            <element name="int6" type="int" nillable="true" />
        </sequence>
    </complexType>
EOF;
test_schema($schema,'type="tns:testTypeWrapper"');
?>
--EXPECTF--
Methods:
  > test(testTypeWrapper $testParam): void

Types:
  > http://test-uri/:testTypeWrapper extends testType
  > http://test-uri/:testType {
    int $int1
    int[] $int2
    int[] $int3
    int[] $int4
    ?int $int5
    ?int $int6
  }
