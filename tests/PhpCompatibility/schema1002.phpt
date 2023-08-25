--TEST--
SOAP XML Schema 18: union with list
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="VoluntaryChangesType">
		<annotation>
			<documentation xml:lang="en">Specifies charges and/or penalties associated with making ticket changes after purchase.</documentation>
		</annotation>
		<sequence minOccurs="0">
			<element name="Penalty" minOccurs="0">
				<annotation>
					<documentation xml:lang="en">Specifies penalty charges as either a currency amount or a percentage of the fare</documentation>
				</annotation>
				<complexType>
					<attribute name="PenaltyType" type="string" use="optional">
						<annotation>
							<documentation xml:lang="en">Indicates the type of penalty involved in the search or response.</documentation>
						</annotation>
					</attribute>
					<attribute name="DepartureStatus" type="string" use="optional">
						<annotation>
							<documentation xml:lang="en">Identifier used to indicate whether the change occurs before or after departure from the origin city.</documentation>
						</annotation>
					</attribute>
				</complexType>
			</element>
		</sequence>
		<attribute name="VolChangeInd" type="boolean" use="optional">
			<annotation>
				<documentation xml:lang="en">Indicator used to specify whether voluntary change and other penalties are involved in the search or response.</documentation>
			</annotation>
		</attribute>
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
    @boolean $VolChangeInd
  }
  > http://test-uri/:Penalty {
    @string $PenaltyType
    @string $DepartureStatus
  }
