--TEST--
SOAP XML Schema 25: SOAP 1.2 Array
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <complexContent>
            <restriction base="enc12:Array" xmlns:enc12="http://www.w3.org/2003/05/soap-encoding">
                <attribute ref="enc12:itemType" wsdl:itemType="int"/>
                <attribute ref="enc12:arraySize" wsdl:arraySize="*"/>
            </restriction>
        </complexContent>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType extends Array = (list<int>)
