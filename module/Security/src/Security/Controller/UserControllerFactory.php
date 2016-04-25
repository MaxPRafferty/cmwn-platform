<?php

namespace Security\Controller;

use Security\Service\SecurityServiceInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class UserControllerFactory
 * @package Security\Controller
 * @codeCoverageIgnore
 */
class UserControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $serviceLocator = $serviceLocator instanceof ServiceLocatorAwareInterface
            ? $serviceLocator->getServiceLocator()
            : $serviceLocator;

        /** @var SecurityServiceInterface $securityService */
        /** @var UserServiceInterface $userService */
        $securityService = $serviceLocator->get(SecurityServiceInterface::class);
        $userService     = $serviceLocator->get(UserServiceInterface::class);
        return new UserController(
            $securityService,
            $userService
        );
    }
}
