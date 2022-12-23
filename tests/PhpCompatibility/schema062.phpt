--TEST--
SOAP XML Schema 62: NULL with attributes
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <simpleContent>
            <restriction base="int">
                <attribute name="int" type="int"/>
            </restriction>
        </simpleContent>
    </complexType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECTF--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType
