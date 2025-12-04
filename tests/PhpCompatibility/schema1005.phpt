--TEST--
SOAP XML Schema 1005: Nested inline elements wrapped in choice element container
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
	<complexType name="LoyaltyTravelInfoType">
        <choice>
          <element name="HotelStayInfo">
            <complexType>
              <sequence>
                <element name="ReservationID" type="string" />
              </sequence>
            </complexType>
          </element>
          <element name="AirFlightInfo">
            <complexType>
              <sequence>
                <element name="FlightSegment" type="string"/>
              </sequence>
            </complexType>
          </element>
        </choice>
      </complexType>
EOF;
test_schema($schema,'type="tns:LoyaltyTravelInfoType"');
?>
--EXPECT--
Methods:
  > test(LoyaltyTravelInfoType $testParam): void

Types:
  > http://test-uri/:LoyaltyTravelInfoType {
    ?LoyaltyTravelInfoTypeHotelStayInfo $HotelStayInfo
    ?LoyaltyTravelInfoTypeAirFlightInfo $AirFlightInfo
  }
  > http://test-uri/:LoyaltyTravelInfoTypeHotelStayInfo {
    ?string $ReservationID
  }
  > http://test-uri/:LoyaltyTravelInfoTypeAirFlightInfo {
    ?string $FlightSegment
  }
