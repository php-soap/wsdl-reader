<?php

declare(strict_types=1);

namespace Soap\WsdlReader\Xml;

use VeeWee\Xml\Xmlns\Xmlns as XmlXmlns;

final class Xmlns
{
    public static function wsdl(): XmlXmlns
    {
        return XmlXmlns::load('http://schemas.xmlsoap.org/wsdl/');
    }

    public static function soap(): XmlXmlns
    {
        return XmlXmlns::load('http://schemas.xmlsoap.org/wsdl/soap/');
    }

    public static function xsd(): XmlXmlns
    {
        return XmlXmlns::load('http://www.w3.org/2001/XMLSchema');
    }
}
