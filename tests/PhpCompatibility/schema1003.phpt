--TEST--
SOAP XML Schema 1002: nested elements within complex types
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <attributeGroup name="VehicleEquipmentPrefGroup">
            <attribute name="Action" type="string" use="optional"/>
    </attributeGroup>
    <element name="SpecialEquipPrefs" minOccurs="0">
        <complexType>
            <sequence>
                <element name="SpecialEquipPref" maxOccurs="15">
                    <complexType>
                        <attributeGroup ref="tns:VehicleEquipmentPrefGroup"/>
                    </complexType>
                </element>
            </sequence>
        </complexType>
    </element>
EOF;
test_schema($schema,'type="tns:SpecialEquipPrefs"');
?>
--EXPECT--
Methods:
  > test(?SpecialEquipPrefs $testParam): void

Types:
  > http://test-uri/:SpecialEquipPrefs {
    array<int<1, 15>, SpecialEquipPref> $SpecialEquipPref
  }
  > http://test-uri/:SpecialEquipPref {
    @?string $Action
  }
