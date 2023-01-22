⚠️⚠️⚠️ **EXPERIMENTAL - FOR EDUCATIONAL USE ONLY** ⚠️⚠️⚠️


# WSDL Reader

This package provides tools for reading WSDL files and to converting them to metadata that will be used in other parts of the php-soap packages. 

# Want to help out? 💚

- [Become a Sponsor](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#sponsor)
- [Let us do your implementation](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#let-us-do-your-implementation)
- [Contribute](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#contribute)
- [Help maintain these packages](https://github.com/php-soap/.github/blob/main/HELPING_OUT.md#maintain)

Want more information about the future of this project? Check out this list of the [next big projects](https://github.com/php-soap/.github/blob/main/PROJECTS.md) we'll be working on.

# Installation

```bash
composer config repositories.wsdlreader git https://github.com/php-soap/wsdl-reader.git
composer require php-soap/wsdl-reader@dev-master
```

## Example usage

```php
use Soap\Engine\Metadata\Model\Method;
use Soap\Engine\Metadata\Model\Type;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Formatter\ShortMethodFormatter;
use Soap\WsdlReader\Formatter\ShortTypeFormatter;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

echo "Reading WSDL $wsdlLocation".PHP_EOL;
$loader = new FlatteningLoader(new StreamWrapperLoader());
$wsdl = (new Wsdl1Reader($loader))($wsdlLocation);

echo "Parsing metadata".PHP_EOL;
$metadataProvider = new Wsdl1MetadataProvider($wsdl);
$metadata = $metadataProvider->getMetadata();
echo PHP_EOL;

echo "Methods:".PHP_EOL;
echo implode(PHP_EOL, $metadata->getMethods()->map(fn (Method $method) => '  > '.(new ShortMethodFormatter())($method)));
echo PHP_EOL.PHP_EOL;

echo "Types:".PHP_EOL;
echo implode(PHP_EOL, $metadata->getTypes()->map(fn (Type $type) => '  > '.(new ShortTypeFormatter())($type)));
echo PHP_EOL.PHP_EOL;
```

As shown above, parsing the WSDL is done in phases:

* Loading
* Reading raw WSDL XML into value objects
* Converting this WSDL to usable metadata

This gives you the flexibility in all different layers:

* You can specify how a WSDL will be loaded.
* You can use the WSDL classes of this package to run your own data lookups/manipulations.
* You can use the provided metadata to run your own data lookups/manipulations.
* ...


## Readers

This package provides some configurable WSDL readers.
This gives you some flexibility in what version of WSDL is being parsed, what SOAP version you want to use, ...

### WSDL1 and 1.1

```php
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Wsdl1Reader;

$wsdl = (new Wsdl1Reader($loader))($wsdlLocation);
$metadataProvider = new Wsdl1MetadataProvider($wsdl, SoapVersion::SOAP_12);
```

This will read the WSDL1 file and parse the SOAP 1.2 information into metadata.
If no SOAP version is specified, it will automatically detect the first SOAP version it encounters.

### WSDL2

Not implemented yet!

## Console

This package provides some console commands that can be used to debug what is inside your WSDL.

```shell
$ ./bin/wsdl-reader

Available commands:
  completion      Dump the shell completion script
  help            Display help for a command
  list            List commands
 inspect
  inspect         Inspects WSDL file.
  inspect:method  Inspects a method of a WSDL file.
  inspect:types   Inspects types from WSDL file.
```

### Listing all contents

```shell
./bin/wsdl-reader inspect your.wsdl
```

### Method details

```shell
./bin/wsdl-reader inspect:method your.wsdl SomeMethodName
```

### Type details

```shell
./bin/wsdl-reader inspect:type your.wsdl SomeType
```

### Custom WSDL Loader
By default, all CLI tools use the StreamWrapperLoader.
All CLI tools have a `--loader=file.php` option that can be used to apply a custom WSDL loader.
This can be handy if your WSDL is located behind authentication or if you want to get control over the HTTP level.

Example custom PHP loader:

```php
<?php

use Soap\Wsdl\Loader\StreamWrapperLoader;

return new StreamWrapperLoader(
    stream_context_create([
        'http' => [
            'method' => 'GET',
            'header'=> sprintf('Authorization: Basic %s', base64_encode('username:password')),
        ],        
    ])
);
```

