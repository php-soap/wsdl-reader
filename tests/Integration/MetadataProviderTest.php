<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Test\Integration;

use Soap\Engine\Metadata\MetadataProvider;
use Soap\EngineIntegrationTests\AbstractMetadataProviderTest;
use Soap\Wsdl\Loader\FlatteningLoader;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Metadata\Wsdl1MetadataProvider;
use Soap\WsdlReader\Wsdl1Reader;

final class MetadataProviderTest extends AbstractMetadataProviderTest
{
    private MetadataProvider $metadataProvider;

    protected function configureForWsdl(string $wsdl): void
    {
        $wsdlInfo = (new Wsdl1Reader(new FlatteningLoader(new StreamWrapperLoader())))($wsdl);
        $this->metadataProvider = new Wsdl1MetadataProvider($wsdlInfo);
    }

    protected function getMetadataProvider(): MetadataProvider
    {
        return $this->metadataProvider;
    }
}
