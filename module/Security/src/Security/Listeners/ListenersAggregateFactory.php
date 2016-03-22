<?php

namespace Security\Listeners;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ListenersAggregateFactory
 */
class ListenersAggregateFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('config');
        $config = isset($config['security-listeners']) ? $config['security-listeners'] : [];

        return new ListenersAggregate($serviceLocator, $config);
    }
}
