<?php

namespace Api\V1\Rest\UserName;

use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserNameResourceFactory
 *
 * Creates a UserNameResource
 */
class UserNameResourceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserServiceInterface $userService */
        $userService = $serviceLocator->get(UserServiceInterface::class);
        return new UserNameResource($userService);
    }
}
