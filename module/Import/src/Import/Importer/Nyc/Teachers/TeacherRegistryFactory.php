<?php

namespace Import\Importer\Nyc\Teachers;

use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class TeacherRegistryFactory
 */
class TeacherRegistryFactory implements FactoryInterface
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
        $userService = $serviceLocator->get('User\UserService');
        return new TeacherRegistry($userService);
    }
}
