<?php

namespace User\Delegator;

use User\Service\UserService;
use Zend\ServiceManager\DelegatorFactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Creates a user Delegator
 *
 * @package User\Delegator
 * @codeCoverageIgnore
 */
class UserDelegatorFactory implements DelegatorFactoryInterface
{
    /**
     * A factory that creates delegates of a given service
     *
     * @param ServiceLocatorInterface $serviceLocator the service locator which requested the service
     * @param string $name the normalized service name
     * @param string $requestedName the requested service name
     * @param callable $callback the callback that is responsible for creating the service
     *
     * @return mixed
     */
    public function createDelegatorWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName, $callback)
    {
        /** @var UserService $userService */
        $userService = call_user_func($callback);
        $delegator   = new UserServiceDelegator($userService);
        return $delegator;
    }
}
