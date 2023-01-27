<?php declare(strict_types=1);

namespace GoetasWebservices\Xsd\XsdToPhp;

use GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle;
use GoetasWebservices\XML\XSDReader\Schema\Schema;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexType;
use GoetasWebservices\XML\XSDReader\Schema\Type\ComplexTypeSimpleContent;
use GoetasWebservices\XML\XSDReader\Schema\Type\SimpleType;
use GoetasWebservices\XML\XSDReader\Schema\Type\Type;
use GoetasWebservices\Xsd\XsdToPhp\Naming\NamingStrategy;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class AbstractConverter
{
    use LoggerAwareTrait;

    protected $baseSchemas = [
        'http://www.w3.org/2001/XMLSchema',
        'http://www.w3.org/XML/1998/namespace',
    ];

    protected $namespaces = [
        'http://www.w3.org/2001/XMLSchema' => '',
        'http://www.w3.org/XML/1998/namespace' => '',
    ];

    /**
     * @var \GoetasWebservices\Xsd\XsdToPhp\Naming\NamingStrategy
     */
    private $namingStrategy;

    abstract public function convert(array $schemas);

    protected $typeAliases = [];

    protected $aliasCache = [];

    public function addAliasMap($ns, $name, callable $handler)
    {
        $this->logger->info("Added map $ns $name");
        $this->typeAliases[$ns][$name] = $handler;
    }

    public function addAliasMapType($ns, $name, $type)
    {
        $this->addAliasMap($ns, $name, static function () use ($type) {
            return $type;
        });
    }

    public function getTypeAliases(): array
    {
        return $this->typeAliases;
    }

    public function getTypeAlias($type, Schema $schemapos = null)
    {
        $schema = $schemapos ?: $type->getSchema();

        $cid = $schema->getTargetNamespace() . '|' . $type->getName();
        if (isset($this->aliasCache[$cid])) {
            return $this->aliasCache[$cid];
        }
        if (isset($this->typeAliases[$schema->getTargetNamespace()][$type->getName()])) {
            return $this->aliasCache[$cid] = call_user_func($this->typeAliases[$schema->getTargetNamespace()][$type->getName()], $type);
        }
    }

    public function __construct(NamingStrategy $namingStrategy, LoggerInterface $logger = null)
    {
        $this->namingStrategy = $namingStrategy;
        $this->logger = $logger ?: new NullLogger();

        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'gYearMonth', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'gMonthDay', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'gMonth', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'gYear', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'NMTOKEN', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'NMTOKENS', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'QName', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'NCName', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'decimal', static function (Type $type) {
            return 'float';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'float', static function (Type $type) {
            return 'float';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'double', static function (Type $type) {
            return 'float';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'string', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'normalizedString', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'integer', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'int', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'unsignedInt', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'negativeInteger', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'positiveInteger', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'nonNegativeInteger', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'nonPositiveInteger', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'long', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'unsignedLong', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'short', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'boolean', static function (Type $type) {
            return 'bool';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'nonNegativeInteger', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'positiveInteger', static function (Type $type) {
            return 'int';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'language', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'token', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'anyURI', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'byte', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'duration', static function (Type $type) {
            return 'DateInterval';
        });

        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'ID', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'IDREF', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'IDREFS', static function (Type $type) {
            return 'string';
        });
        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'Name', static function (Type $type) {
            return 'string';
        });

        $this->addAliasMap('http://www.w3.org/2001/XMLSchema', 'NCName', static function (Type $type) {
            return 'string';
        });
    }

    /**
     * @return \GoetasWebservices\Xsd\XsdToPhp\Naming\NamingStrategy
     */
    protected function getNamingStrategy()
    {
        return $this->namingStrategy;
    }

    public function addNamespace($ns, $phpNamespace)
    {
        $this->logger->info("Added ns mapping $ns, $phpNamespace");
        $this->namespaces[$ns] = $phpNamespace;

        return $this;
    }

    protected function cleanName($name)
    {
        return preg_replace('/<.*>/', '', $name);
    }

    /**
     * @return \GoetasWebservices\XML\XSDReader\Schema\Type\Type|null
     */
    protected function isArrayType(Type $type)
    {
        if ($type instanceof SimpleType) {
            if ($type->getList()) {
                return $type->getList();
            } elseif (($rst = $type->getRestriction()) && $rst->getBase() instanceof SimpleType) {
                return $this->isArrayType($rst->getBase());
            }
        } elseif ($type instanceof ComplexTypeSimpleContent) {
            if ($ext = $type->getExtension()) {
                return $this->isArrayType($ext->getBase());
            }
        }

        return  null;
    }

    /**
     * @return \GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle|null
     */
    protected function isArrayNestedElement(Type $type)
    {
        if ($type instanceof ComplexType && !$type->getParent() && !$type->getAttributes() && count($type->getElements()) === 1) {
            $elements = $type->getElements();

            return $this->isArrayElement(reset($elements));
        }
    }

    /**
     * @param mixed $element
     *
     * @return \GoetasWebservices\XML\XSDReader\Schema\Element\ElementSingle|null
     */
    protected function isArrayElement($element)
    {
        if ($element instanceof ElementSingle && ($element->getMax() > 1 || $element->getMax() === -1)) {
            return $element;
        }
    }

    public function getNamespaces()
    {
        return $this->namespaces;
    }
}
