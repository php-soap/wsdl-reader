--TEST--
SOAP XML Schema 1001: Prepend element name before attribute type names for more unique type-names.
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="VehicleCoreType">
        <sequence>
            <element name="VehType" minOccurs="0" type="string" />
        </sequence>
        <attribute name="DriveType" use="optional">
            <simpleType>
                <restriction base="NMTOKEN">
                    <enumeration value="AWD" />
                    <enumeration value="4WD" />
                    <enumeration value="Unspecified" />
                </restriction>
            </simpleType>
        </attribute>
    </complexType>
EOF;
test_schema($schema,'type="tns:VehicleCoreType"');
?>
--EXPECT--
Methods:
  > test(VehicleCoreType $testParam): void

Types:
  > http://test-uri/:VehicleCoreType {
    ?string $VehType
    @?VehicleCoreTypeDriveType in (AWD|4WD|Unspecified) $DriveType
  }
