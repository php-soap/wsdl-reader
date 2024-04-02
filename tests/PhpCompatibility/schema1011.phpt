--TEST--
SOAP XML Schema 1001: Empty enumarable
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="EmptySimpleType">
        <restriction base="string">
            <enumeration value=""/>
        </restriction>
    </simpleType>
EOF;
test_schema($schema,'type="tns:EmptySimpleType"');
?>
--EXPECT--
Methods:
  > test(EmptySimpleType $testParam): void

Types:
  > http://test-uri/:EmptySimpleType extends string in ()

