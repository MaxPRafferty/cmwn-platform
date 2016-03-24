<?php

namespace Api\V1\Rest\User;

use User\Service\UserServiceInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserResourceFactory
 * @package Api\V1\Rest\User
 */
class UserResourceFactory
{
    /**
     * @param ServiceLocatorInterface $services
     * @return UserResource
     */
    public function __invoke(ServiceLocatorInterface $services)
    {
        /** @var UserServiceInterface $userService */
        $userService = $services->get('User\Service');

        return new UserResource($userService);
    }
}
