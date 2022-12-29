--TEST--
SOAP XML Schema 8: simpleType/restriction (anonymous, inside an ellement)
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
<element name="testElement">
    <simpleType>
        <restriction>
            <simpleType name="testType2">
            <restriction base="xsd:int"/>
        </simpleType>
      </restriction>
    </simpleType>
</element>
EOF;
test_schema($schema,'element="tns:testElement"');
?>
--EXPECT--
Methods:
  > test(testElement $testParam): void

Types:
  > http://test-uri/:testType2 extends integer
  > http://test-uri/:testElement extends testType2
