<?php

namespace Security\Authentication;

use Interop\Container\ContainerInterface;
use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class AuthAdapterFactory
 */
class AuthAdapterFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new AuthAdapter(
            $container->get(SecurityServiceInterface::class)
        );
    }
}
