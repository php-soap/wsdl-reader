--TEST--
SOAP XML Schema 66: Required Attribute
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="immobile">
        <sequence>
            <element name="indirizzo" type="string" maxOccurs="1" minOccurs="0"/>
            <element name="numero" type="string" maxOccurs="1" minOccurs="0"/>
            <element name="zona" type="string" maxOccurs="1" minOccurs="0"/>
        </sequence>
        <attribute name="id" type="string"/>
        <attribute name="id-agenzia" type="int"/>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:immobile {
    ?string $indirizzo
    ?string $numero
    ?string $zona
    @?string $id
    @?int $id-agenzia
  }
