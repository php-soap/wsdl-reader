--TEST--
SOAP XML Schema 1007: Deeply nested complex-type declarations inside complexTypes
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="LocationType">
		<simpleContent>
			<extension base="string">
			    <attribute name="LocationCode" type="string" use="optional" />
			</extension>
		</simpleContent>
	</complexType>
	<complexType name="VerificationType">
        <sequence>
            <element name="Email" type="string" minOccurs="0" />
            <element name="StartLocation" minOccurs="0">
                <complexType>
                    <simpleContent>
                        <extension base="tns:LocationType">
                            <attribute name="AssociatedDateTime" type="dateTime" use="optional" />
                        </extension>
                    </simpleContent>
                </complexType>
            </element>
        </sequence>
    </complexType>

EOF;
test_schema($schema,'type="tns:VerificationType"');
?>
--EXPECT--
Methods:
  > test(VerificationType $testParam): void

Types:
  > http://test-uri/:LocationType extends string {
    string $_
    @string $LocationCode
  }
  > http://test-uri/:VerificationType {
    ?string $Email
    ?StartLocation $StartLocation
  }
  > http://test-uri/:StartLocation extends LocationType {
    string $_
    @string $LocationCode
    @dateTime $AssociatedDateTime
  }
