--TEST--
SOAP XML Schema 61: SOAP 1.2 Multidimensional array (second way, literal encoding)
--SKIPIF--
<?php exit('skip: enc12 Array does not work yet'); ?>
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <complexContent>
            <restriction base="enc12:Array" xmlns:enc12="http://www.w3.org/2003/05/soap-encoding">
                <all>
                    <element name="x_item" type="int" maxOccurs="unbounded"/>
            </all>
        <attribute ref="enc12:arraySize" wsdl:arraySize="* 1"/>
        </restriction>
    </complexContent>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"','rpc','literal');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType
