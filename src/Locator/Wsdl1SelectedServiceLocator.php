<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Locator;

use Psl\Option\Option;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Model\Service\Wsdl1SelectedService;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Parser\Xml\QnameParser;
use Soap\WsdlReader\Todo\OptionsHelper;

/**
 * @see https://stackoverflow.com/questions/20891198/details-on-wsdl-ports
 * Searches best fit for services -> ports -> bindings
 */
class Wsdl1SelectedServiceLocator
{
    public function __invoke(Wsdl1 $wsdl, ?SoapVersion $preferredVersion): Wsdl1SelectedService
    {
        foreach ($wsdl->services->items as $service) {
            $port = $service->ports->lookupBySoapVersion($preferredVersion);
            $binding = OptionsHelper::andThen($port, static fn (Port $port): Option => $wsdl->bindings->lookupByQName($port->binding));
            $portType = OptionsHelper::andThen($binding, static fn (Binding $binding): Option => $wsdl->portTypes->lookupByQName($binding->type));

            if ($portType->isSome()) {
                $selectedService = new Wsdl1SelectedService(
                    service: $service,
                    port: $port->unwrap(),
                    binding: $binding->unwrap(),
                    portType: $portType->unwrap(),
                    messages: $wsdl->messages,
                    namespaces: $wsdl->namespaces,
                );

                return $selectedService;
            }
        }

        // TODO -> Error type
        throw new \InvalidArgumentException('Could not find the requested service in the WSDL file!');
    }
}
