--TEST--
SOAP XML Schema 49: Restriction of complex type (2)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType2">
        <sequence>
            <element name="int" type="int"/>
            <element name="int2" type="int"/>
        </sequence>
    </complexType>
    <complexType name="testType">
        <complexContent>
            <restriction base="tns:testType2">
                <sequence>
                    <element name="int2" type="int"/>
                </sequence>
            </restriction>
        </complexContent>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType2 {
    int $int
    int $int2
  }
  > http://test-uri/:testType extends testType2
