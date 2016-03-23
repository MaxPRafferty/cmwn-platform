<?php

namespace Application\Listeners;

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
        $config = isset($config['shared-listeners']) ? $config['shared-listeners'] : [];

        return new ListenersAggregate($serviceLocator, $config);
    }
}
