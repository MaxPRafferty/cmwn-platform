<?php

namespace Application\Service;

use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * A Factory that will check each of the options against the SM for injection
 */
class DependentInvokableFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $options = $options ?? [];
        $arguments = array_map(
            function ($specValue) use (&$container) {
                // Check for a dependency from the SM
                if (is_string($specValue) && $container->has($specValue)) {
                    return $container->get($specValue);
                }

                return $specValue;
            },
            $options
        );

        return new $requestedName(...array_values($arguments));
    }
}
