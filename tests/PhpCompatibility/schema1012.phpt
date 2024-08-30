--TEST--
SOAP XML Schema 1001: Any elements
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="GetCustomerDetailsRequest">
        <complexType>
            <sequence>
                <element name="customerId" type="xsd:string" />
                <element name="countryCode" type="xsd:string" nillable="true" />
                <any processContents="strict" maxOccurs="0"  />
            </sequence>
        </complexType>
    </element>
EOF;
test_schema($schema,'type="tns:GetCustomerDetailsRequest"');
?>
--EXPECT--
Methods:
  > test(GetCustomerDetailsRequest $testParam): void

Types:
  > http://test-uri/:GetCustomerDetailsRequest {
    string $customerId
    ?string $countryCode
    any $any
  }

