--TEST--
SOAP XML Schema 55: Apache Map (extension)
--INI--
precision=14
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="testType">
        <complexContent>
            <extension base="apache:Map" xmlns:apache="http://xml.apache.org/xml-soap">
        </extension>
    </complexContent>
    </complexType>
EOF;
test_schema($schema,'type="testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType extends array
