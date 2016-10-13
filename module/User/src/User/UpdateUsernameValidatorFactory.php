<?php

namespace User;

use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class UpdateUsernameValidatorFactory
 * @package User
 */
class UpdateUsernameValidatorFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     * @return UpdateUsernameValidator
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;
        /**@var UserServiceInterface $userService*/
        $userService = $serviceLocator->get(UserServiceInterface::class);
        return new UpdateUsernameValidator([], $userService);
    }
}
