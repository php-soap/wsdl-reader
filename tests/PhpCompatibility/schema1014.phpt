--TEST--
SOAP XML Schema 1001: Prepend parent type name before local simple element type names for more unique type-names.
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <element name="Element">
        <complexType>
            <sequence>
                <element name="Enum">
                    <simpleType>
                        <restriction base="string">
                            <enumeration value="foo" />
                            <enumeration value="bar" />
                        </restriction>
                    </simpleType>
                </element>
            </sequence>
        </complexType>
    </element>
EOF;
test_schema($schema,'type="tns:Element"');
?>
--EXPECT--
Methods:
  > test(Element $testParam): void

Types:
  > http://test-uri/:Element {
    ElementEnum in (foo|bar) $Enum
  }
