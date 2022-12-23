--TEST--
SOAP XML Schema 21: list of unions
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <simpleType name="testType">
        <list>
            <simpleType>
                <union memberTypes="int float string"/>
            </simpleType>
        </list>
    </simpleType>
EOF;
test_schema($schema,'type="tns:testType"');
?>
--EXPECT--
Methods:
  > test(testType $testParam): void

Types:
  > http://test-uri/:testType
