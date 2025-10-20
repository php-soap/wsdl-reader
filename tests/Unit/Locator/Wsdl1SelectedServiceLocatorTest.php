<?php declare(strict_types=1);

namespace Soap\WsdlReader\Test\Unit\Locator;

use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Soap\Wsdl\Loader\StreamWrapperLoader;
use Soap\WsdlReader\Exception\ServiceException;
use Soap\WsdlReader\Locator\ServiceSelectionCriteria;
use Soap\WsdlReader\Locator\Wsdl1SelectedServiceLocator;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Wsdl1Reader;

final class Wsdl1SelectedServiceLocatorTest extends TestCase
{
    #[DataProvider('provideServiceLocations')]
    public function test_it_can_locate_service(
        string $wsdl,
        ServiceSelectionCriteria $criteria,
        Closure $assert
    ): void {

        $locator = new Wsdl1SelectedServiceLocator();
        $wsdl1 = (new Wsdl1Reader(new StreamWrapperLoader()))($wsdl);

        $service = $locator($wsdl1, $criteria);
        $assert($service);
    }

    #[DataProvider('provideNotLocatableServices')]
    public function test_it_can_not_locate_service(
        string $wsdl,
        ServiceSelectionCriteria $criteria,
    ): void {
        $this->expectException(ServiceException::class);

        $locator = new Wsdl1SelectedServiceLocator();
        $wsdl1 = (new Wsdl1Reader(new StreamWrapperLoader()))($wsdl);

        $locator($wsdl1, $criteria);
    }

    public static function provideServiceLocations(): iterable
    {
        $weatherWs = FIXTURE_DIR . '/wsdl/weather-ws.wsdl';

        yield 'first-default' => [
            $weatherWs,
            ServiceSelectionCriteria::defaults(),
            self::assertSoapWeather11Service(...),
        ];

        yield 'first-http' => [
            $weatherWs,
            ServiceSelectionCriteria::defaults()
                ->withServiceName('Weather')
                ->withPortName('WeatherHttpGet')
                ->withAllowHttpPorts(),
            self::assertSoapHttpGetService(...),
        ];

        yield 'first-soap12' => [
            $weatherWs,
            ServiceSelectionCriteria::defaults()
                ->withPreferredSoapVersion(SoapVersion::SOAP_12),
            self::assertSoapWeather12Service(...),
        ];
    }

    public static function provideNotLocatableServices(): iterable
    {
        $weatherWs = FIXTURE_DIR . '/wsdl/weather-ws.wsdl';

        yield 'invalid-service-name' => [
            $weatherWs,
            ServiceSelectionCriteria::defaults()->withServiceName('invalid'),
        ];

        yield 'invalid-port-name' => [
            $weatherWs,
            ServiceSelectionCriteria::defaults()->withPortName('invalid'),
        ];

    }

    private static function assertSoapWeather11Service(Wsdl1SelectedService $service): void
    {
        static::assertSame('Weather', $service->service->name);
        static::assertSame('WeatherSoap', $service->port->name);
        static::assertSame('http://wsf.cdyne.com/WeatherWS/Weather.asmx', $service->port->address->location);
        static::assertSame(true, $service->port->address->type->isSoap());
        static::assertSame(SoapVersion::SOAP_11, $service->port->address->type->soapVersion());
        static::assertSame('WeatherSoap', $service->binding->name);
        static::assertSame('WeatherSoap', $service->portType->name);
        static::assertSame('GetWeatherInformationSoapIn', $service->messages->items[0]->name);
    }

    private static function assertSoapWeather12Service(Wsdl1SelectedService $service): void
    {
        static::assertSame('Weather', $service->service->name);
        static::assertSame('WeatherSoap12', $service->port->name);
        static::assertSame('http://wsf.cdyne.com/WeatherWS/Weather.asmx', $service->port->address->location);
        static::assertSame(true, $service->port->address->type->isSoap());
        static::assertSame(SoapVersion::SOAP_12, $service->port->address->type->soapVersion());
        static::assertSame('WeatherSoap12', $service->binding->name);
        static::assertSame('WeatherSoap', $service->portType->name);
        static::assertSame('GetWeatherInformationSoapIn', $service->messages->items[0]->name);
    }

    private static function assertSoapHttpGetService(Wsdl1SelectedService $service): void
    {
        static::assertSame('Weather', $service->service->name);
        static::assertSame('WeatherHttpGet', $service->port->name);
        static::assertSame('http://wsf.cdyne.com/WeatherWS/Weather.asmx', $service->port->address->location);
        static::assertSame(false, $service->port->address->type->isSoap());
        static::assertSame(null, $service->port->address->type->soapVersion());
        static::assertSame('WeatherHttpGet', $service->binding->name);
        static::assertSame('WeatherHttpGet', $service->portType->name);
        static::assertSame('GetWeatherInformationSoapIn', $service->messages->items[0]->name);
    }
}
