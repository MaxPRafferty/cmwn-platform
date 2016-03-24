<?php

namespace Api\Factory;

use Api\Listeners\UserRouteListener;
use User\Service\UserServiceInterface;
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
        /** @var UserServiceInterface $userService */
        $userService = $serviceLocator->get('User\Service');

        return new UserRouteListener($userService);
    }
}
