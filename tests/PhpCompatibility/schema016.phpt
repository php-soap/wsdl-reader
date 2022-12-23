--TEST--
SOAP XML Schema 16: simpleType/union (inline type)
--INI--
precision=14
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <union>
            <simpleType>
                <restriction base="string"/>
            </simpleType>
            <simpleType>
                <restriction base="int"/>
            </simpleType>
            <simpleType>
                <restriction base="float"/>
            </simpleType>
        </union>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType
