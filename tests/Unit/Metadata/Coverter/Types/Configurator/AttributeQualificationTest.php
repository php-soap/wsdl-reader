<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Metadata\Coverter\Types\Configurator;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Soap\Engine\Metadata\Metadata;
use Soap\Engine\Metadata\Model\Property;
use Soap\Wsdl\Loader\CallbackLoader;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

final class AttributeQualificationTest extends TestCase
{
    private const TNS = 'https://test';
    private const OTHER = 'urn:other';
    private const QUALIFIED = 'urn:qualified';
    private const XML = 'http://www.w3.org/XML/1998/namespace';

    private static ?Metadata $metadata = null;

    #[DataProvider('provideAttributes')]
    public function test_it_detects_attribute_qualification(
        string $typeName,
        string $propertyName,
        bool $expectedQualified,
        string $expectedTargetNamespace,
    ): void {
        $property = self::findProperty($typeName, $propertyName);
        $type = $property->getType();

        static::assertTrue($type->getMeta()->isAttribute()->unwrapOr(false));
        static::assertSame($expectedQualified, $type->getMeta()->isQualified()->unwrapOr(false));
        static::assertSame($expectedTargetNamespace, $type->getXmlTargetNamespace());
    }

    public static function provideAttributes(): iterable
    {
        yield 'ref to xml:lang' => ['Direct', 'lang', true, self::XML];
        yield 'ref to global attribute in foreign schema' => ['Direct', 'flag', true, self::OTHER];
        yield 'ref to global attribute in same schema' => ['Direct', 'glob', true, self::TNS];
        yield 'local unqualified attribute' => ['Direct', 'plain', false, self::TNS];
        yield 'local form=qualified attribute' => ['Direct', 'qual', true, self::TNS];

        yield 'attributeFormDefault=qualified local attribute' => ['QualifiedByDefault', 'local', true, self::QUALIFIED];
        yield 'attributeFormDefault=qualified local form=unqualified attribute' => ['QualifiedByDefault', 'unq', false, self::QUALIFIED];

        yield 'ref inside attributeGroup' => ['Grouped', 'glob', true, self::TNS];
        yield 'xml:lang inside attributeGroup' => ['Grouped', 'lang', true, self::XML];
        yield 'local inside attributeGroup' => ['Grouped', 'grouped', false, self::TNS];
        yield 'ref inside nested attributeGroup' => ['Grouped', 'flag', true, self::OTHER];

        yield 'ref inherited from complexContent extension base' => ['Extended', 'glob', true, self::TNS];
        yield 'ref declared on complexContent extension' => ['Extended', 'lang', true, self::XML];
        yield 'local declared on complexContent extension' => ['Extended', 'extra', false, self::TNS];

        yield 'ref declared on complexContent restriction' => ['Restricted', 'glob', true, self::TNS];

        yield 'ref declared on simpleContent extension' => ['Text', 'lang', true, self::XML];
        yield 'local declared on simpleContent extension' => ['Text', 'plain', false, self::TNS];
    }

    private static function findProperty(string $typeName, string $propertyName): Property
    {
        $type = self::metadata()->getTypes()->fetchFirstByName($typeName);
        foreach ($type->getProperties() as $property) {
            if ($property->getName() === $propertyName) {
                return $property;
            }
        }

        static::fail(sprintf('Property %s not found on type %s', $propertyName, $typeName));
    }

    private static function metadata(): Metadata
    {
        if (self::$metadata) {
            return self::$metadata;
        }

        $tns = self::TNS;
        $other = self::OTHER;
        $qualified = self::QUALIFIED;
        $wsdl = <<<EOXML
        <definitions xmlns:xsd="http://www.w3.org/2001/XMLSchema"
                     xmlns:tns="{$tns}"
                     xmlns:o="{$other}"
                     xmlns:soap="http://schemas.xmlsoap.org/wsdl/soap/"
                     xmlns="http://schemas.xmlsoap.org/wsdl/"
                     targetNamespace="{$tns}">
            <types>
                <schema xmlns="http://www.w3.org/2001/XMLSchema" targetNamespace="{$other}">
                    <attribute name="flag" type="xsd:string"/>
                    <attributeGroup name="Nested">
                        <attribute ref="o:flag"/>
                    </attributeGroup>
                </schema>
                <schema xmlns="http://www.w3.org/2001/XMLSchema" targetNamespace="{$qualified}" attributeFormDefault="qualified">
                    <complexType name="QualifiedByDefault">
                        <attribute name="local" type="xsd:string"/>
                        <attribute name="unq" type="xsd:string" form="unqualified"/>
                    </complexType>
                </schema>
                <schema xmlns="http://www.w3.org/2001/XMLSchema" targetNamespace="{$tns}">
                    <import namespace="http://www.w3.org/XML/1998/namespace"/>
                    <import namespace="{$other}"/>
                    <attribute name="glob" type="xsd:string"/>
                    <attributeGroup name="Group">
                        <attribute ref="tns:glob"/>
                        <attribute ref="xml:lang"/>
                        <attribute name="grouped" type="xsd:string"/>
                        <attributeGroup ref="o:Nested"/>
                    </attributeGroup>
                    <complexType name="Direct">
                        <sequence><element name="v" type="xsd:string"/></sequence>
                        <attribute ref="xml:lang"/>
                        <attribute ref="o:flag"/>
                        <attribute ref="tns:glob"/>
                        <attribute name="plain" type="xsd:string"/>
                        <attribute name="qual" type="xsd:string" form="qualified"/>
                    </complexType>
                    <complexType name="Grouped">
                        <attributeGroup ref="tns:Group"/>
                    </complexType>
                    <complexType name="Base">
                        <attribute ref="tns:glob"/>
                    </complexType>
                    <complexType name="Extended">
                        <complexContent>
                            <extension base="tns:Base">
                                <attribute ref="xml:lang"/>
                                <attribute name="extra" type="xsd:string"/>
                            </extension>
                        </complexContent>
                    </complexType>
                    <complexType name="Restricted">
                        <complexContent>
                            <restriction base="tns:Base">
                                <attribute ref="tns:glob" use="required"/>
                            </restriction>
                        </complexContent>
                    </complexType>
                    <complexType name="Text">
                        <simpleContent>
                            <extension base="xsd:string">
                                <attribute ref="xml:lang"/>
                                <attribute name="plain" type="xsd:string"/>
                            </extension>
                        </simpleContent>
                    </complexType>
                </schema>
            </types>
            <message name="m"><part name="p" type="tns:Direct"/></message>
            <portType name="pt"><operation name="test"><input message="tns:m"/></operation></portType>
            <binding name="b" type="tns:pt">
                <soap:binding style="rpc" transport="http://schemas.xmlsoap.org/soap/http"/>
                <operation name="test">
                    <soap:operation soapAction="#test"/>
                    <input><soap:body use="literal" namespace="{$tns}"/></input>
                </operation>
            </binding>
            <service name="s"><port name="p" binding="tns:b"><soap:address location="test://"/></port></service>
        </definitions>
        EOXML;

        $info = (new Wsdl1Reader(new CallbackLoader(static fn (): string => $wsdl)))('some.wsdl');

        return self::$metadata = (new Wsdl1MetadataProvider($info))->getMetadata();
    }
}
