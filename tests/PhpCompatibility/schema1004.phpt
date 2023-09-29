--TEST--
SOAP XML Schema 1004: Nested simple types
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
	<simpleType name="StringLength1to128">
		<restriction base="string">
			<minLength value="1"/>
			<maxLength value="128"/>
		</restriction>
	</simpleType>
	<complexType name="EmailType">
        <simpleContent>
            <extension base="tns:StringLength1to128">
                <attribute name="EmailType" type="string" use="optional" />
            </extension>
        </simpleContent>
    </complexType>
    <complexType name="VerificationType">
		<sequence>
			<element name="Email" type="tns:EmailType" minOccurs="0">
			</element>
		</sequence>
	</complexType>
EOF;
test_schema($schema,'type="tns:SpecialEquipPrefs"');
?>
--EXPECT--
Methods:
  > test(SpecialEquipPrefs $testParam): void

Types:
  > http://test-uri/:StringLength1to128 extends string
  > http://test-uri/:EmailType extends StringLength1to128 {
    StringLength1to128 $_
    @?string $EmailType
  }
  > http://test-uri/:VerificationType {
    ?EmailType $Email
  }
