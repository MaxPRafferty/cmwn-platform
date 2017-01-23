<?php

namespace Api\Factory;

use Api\Listeners\ScopeListener;
use Interop\Container\ContainerInterface;
use Security\Service\SecurityGroupServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ScopeListenerFactory
 */
class ScopeListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ScopeListener($container->get(SecurityGroupServiceInterface::class));
    }
}
