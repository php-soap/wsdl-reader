--TEST--
SOAP XML Schema 18: union with list
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <xsd:simpleType name="PhoneTypeEnum">
        <xsd:restriction base="xsd:string">
            <xsd:enumeration value="Home"/>
            <xsd:enumeration value="Office"/>
            <xsd:enumeration value="Gsm"/>
        </xsd:restriction>
    </xsd:simpleType>
EOF;
test_schema($schema,'type="tns:PhoneTypeEnum"');
?>
--EXPECT--
Methods:
  > test(PhoneTypeEnum $testParam): void

Types:
  > http://test-uri/:PhoneTypeEnum extends string in (Home|Office|Gsm)

