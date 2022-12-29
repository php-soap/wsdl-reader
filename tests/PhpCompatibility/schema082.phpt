--TEST--
SOAP XML Schema 82: SOAP 1.1 Array with SOAP_USE_XSI_ARRAY_TYPE (second way)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <complexContent>
            <restriction base="SOAP-ENC:Array">
                <all>
                    <element name="x_item" type="int" maxOccurs="unbounded"/>
            </all>
        </restriction>
    </complexContent>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"',"rpc","encoded",'',SOAP_USE_XSI_ARRAY_TYPE);
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType extends Array
