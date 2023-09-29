--TEST--
SOAP XML Schema 1002: nested complex types
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="VoluntaryChangesType">
		<sequence minOccurs="0">
			<element name="Penalty" minOccurs="0">
				<complexType>
					<attribute name="PenaltyType" type="string" use="optional"/>
					<attribute name="DepartureStatus" type="string" use="optional"/>
				</complexType>
			</element>
		</sequence>
		<attribute name="VolChangeInd" type="boolean" use="optional"/>
	</complexType>
EOF;
test_schema($schema,'type="tns:VoluntaryChangesType"');
?>
--EXPECT--
Methods:
  > test(VoluntaryChangesType $testParam): void

Types:
  > http://test-uri/:VoluntaryChangesType {
    ?Penalty $Penalty
    @?boolean $VolChangeInd
  }
  > http://test-uri/:Penalty {
    @?string $PenaltyType
    @?string $DepartureStatus
  }
