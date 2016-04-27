<?php

namespace Api\Factory;

use Api\Listeners\UserRouteListener;
use Security\Authorization\Assertions\UserAssertion;
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
        /** @var UserAssertion $userAssertion */
        $userService   = $serviceLocator->get(UserServiceInterface::class);
        $userAssertion = $serviceLocator->get(UserAssertion::class);
        return new UserRouteListener($userService, $userAssertion);
    }
}
