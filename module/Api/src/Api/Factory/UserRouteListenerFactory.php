<?php

namespace Api\Factory;

use Api\Listeners\UserRouteListener;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserRouteListenerFactory
 * @package Api\Factory
 */
class UserRouteListenerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new UserRouteListener($serviceLocator->get('User\Service'));
    }
}
