⚠️⚠️⚠️ **EXPERIMENTAL - FOR EDUCATIONAL USE ONLY** ⚠️⚠️⚠️


# WSDL Reader

This package provides tools for reading WSDL files and to converting them to usable metadata. 

# Want to help out? 💚

- [Become a Sponsor](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#sponsor)
- [Let us do your implementation](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#let-us-do-your-implementation)
- [Contribute](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#contribute)
- [Help maintain these packages](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#maintain)

Want more information about the future of this project? Check out this list of the [next big projects](https://github.com/php-soap/.github/blob/main/PROJECTS.md) we'll be working on.

# Installation

```bash
composer config repositories.wsdlreader git https://github.com/php-soap/wsdl.git
composer require php-soap/wsdl-reader
```

## Usage

```php
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Formatter\MethodFormatter;
use Soap\WsdlReader\Formatter\ShortTypeFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

echo "Reading WSDL $wsdlLocation".PHP_EOL;
$wsdl = (new Wsdl1Reader(new FlatteningLoader(new StreamWrapperLoader())))($wsdlLocation);

echo "Parsing metadata".PHP_EOL;
$metadataProvider = new Wsdl1MetadataProvider($wsdl);
$metadata = $metadataProvider->getMetadata();
echo PHP_EOL;

echo "Methods:".PHP_EOL;
echo implode(PHP_EOL, $metadata->getMethods()->map(fn (Method $method) => '  > '.(new MethodFormatter())($method)));
echo PHP_EOL.PHP_EOL;

echo "Types:".PHP_EOL;
echo implode(PHP_EOL, $metadata->getTypes()->map(fn (Type $type) => '  > '.(new ShortTypeFormatter())($type)));
echo PHP_EOL.PHP_EOL;
```

