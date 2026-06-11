--TEST--
SOAP XML Schema 1017: Repeating group ref around a choice exposes members as optional unbounded lists
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <group name="Operation">
        <choice>
            <element name="delete" type="string" />
            <element name="write" type="string" />
        </choice>
    </group>
    <complexType name="Message">
        <sequence>
            <element minOccurs="0" name="transactionId" type="string" />
            <group minOccurs="0" maxOccurs="unbounded" ref="tns:Operation" />
        </sequence>
    </complexType>
EOF;
test_schema($schema,'type="tns:Message"');
?>
--EXPECT--
Methods:
  > test(Message $testParam): void

Types:
  > http://test-uri/:Message {
    ?string $transactionId
    array<int<0, max>, string> $delete
    array<int<0, max>, string> $write
  }
