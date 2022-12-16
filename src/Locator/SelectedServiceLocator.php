<?php
declare(strict_types=1);

namespace Soap\WsdlReader\Locator;

use Psl\Option\Option;
use Soap\WsdlReader\Model\Definitions\Binding;
use Soap\WsdlReader\Model\Definitions\Port;
use Soap\WsdlReader\Model\Definitions\PortType;
use Soap\WsdlReader\Model\Definitions\SelectedService;
use Soap\WsdlReader\Model\Definitions\SoapVersion;
use Soap\WsdlReader\Model\Wsdl1;
use Soap\WsdlReader\Todo\OptionsHelper;

/**
 * @see https://stackoverflow.com/questions/20891198/details-on-wsdl-ports
 * Searches best fit for services -> ports -> bindings
 */
class SelectedServiceLocator
{
    public function __invoke(Wsdl1 $wsdl, ?SoapVersion $preferredVersion): SelectedService
    {
        foreach ($wsdl->services->items as $service) {
            $port = $service->ports->lookupBySoapVersion($preferredVersion);
            $binding = OptionsHelper::andThen($port, static fn (Port $port): Option => $wsdl->bindings->lookupByIdentifier($port->binding));
            $portType = OptionsHelper::andThen($binding, static fn (Binding $binding): Option => $wsdl->portTypes->lookupByName($binding->name));

            if ($port->and($binding)->and($portType)->isSome()) {
                $selectedService = new SelectedService(
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
