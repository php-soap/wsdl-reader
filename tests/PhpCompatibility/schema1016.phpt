--TEST--
SOAP XML Schema 1016: Group ref with minOccurs / MaxOccurs
--FILE--
<?php
include __DIR__."/test_schema.inc";
$schema = <<<EOF
    <complexType name="Code">
        <sequence>
            <element form="unqualified" name="code" type="string" />
        </sequence>
    </complexType>
    <simpleType name="SomeSpecificTypeCodeEnum">
        <restriction base="string">
            <enumeration value="foo" />
            <enumeration value="bar" />
        </restriction>
    </simpleType>
    <complexType name="SomeType">
        <choice>
            <element name="code">
                <complexType>
                    <complexContent>
                        <restriction base="tns:Code">
                            <sequence>
                                <element form="unqualified" name="code" type="tns:SomeSpecificTypeCodeEnum" />
                            </sequence>
                        </restriction>
                    </complexContent>
                </complexType>
            </element>
        </choice>
    </complexType>
EOF;
test_schema($schema,'type="tns:Element"');
?>
--EXPECT--
Methods:
  > test(Element $testParam): void

Types:
  > http://test-uri/:Code {
    string $code
  }
  > http://test-uri/:SomeSpecificTypeCodeEnum extends string in (foo|bar)
  > http://test-uri/:SomeType {
    ?SomeTypeCode $code
  }
  > http://test-uri/:SomeTypeCode extends Code {
    ?SomeSpecificTypeCodeEnum in (foo|bar) $code
  }
