--TEST--
SOAP XML Schema 1015: Group ref with minOccurs / MaxOccurs
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="nullableGroupRef">
        <sequence>
            <group minOccurs="0" ref="tns:mygroup"/>
        </sequence>
    </complexType>
    <complexType name="listGroupRef">
        <sequence>
            <group minOccurs="0" maxOccurs="unbounded" ref="tns:mygroup"/>
        </sequence>
    </complexType>
    <complexType name="scopedGroupRef">
        <sequence>
            <group minOccurs="2" maxOccurs="6" ref="tns:mygroup"/>
        </sequence>
    </complexType>
    <complexType name="singleGroupRef">
        <sequence>
            <group minOccurs="1" maxOccurs="1" ref="tns:mygroup"/>
        </sequence>
    </complexType>
    <group name="mygroup">
        <sequence>
            <element name="nullable" type="string" minOccurs="0" />
            <element name="list" type="string" minOccurs="0" maxOccurs="unbounded" />
            <element name="scoped" type="string" minOccurs="3" maxOccurs="5" />
            <element name="single" type="string" minOccurs="1" maxOccurs="1"/>
        </sequence>
    </group>
EOF;
test_schema($schema,'type="tns:Element"');
?>
--EXPECT--
Methods:
  > test(Element $testParam): void

Types:
  > http://test-uri/:nullableGroupRef {
    ?string $nullable
    array<int<0, max>, string> $list
    array<int<0, 5>, string> $scoped
    ?string $single
  }
  > http://test-uri/:listGroupRef {
    array<int<0, max>, string> $nullable
    array<int<0, max>, string> $list
    array<int<0, max>, string> $scoped
    array<int<0, max>, string> $single
  }
  > http://test-uri/:scopedGroupRef {
    array<int<0, 6>, string> $nullable
    array<int<0, max>, string> $list
    array<int<6, 30>, string> $scoped
    array<int<2, 6>, string> $single
  }
  > http://test-uri/:singleGroupRef {
    ?string $nullable
    array<int<0, max>, string> $list
    array<int<3, 5>, string> $scoped
    string $single
  }
