<?php

namespace Api\Factory;

use Api\Listeners\GroupRouteListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class GroupRouteListenerFactory
 *
 * ${CARET}
 */
class GroupRouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new GroupRouteListener($serviceLocator->get('Group\Service\GroupService'));
    }
}
