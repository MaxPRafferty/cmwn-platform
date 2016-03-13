<?php

namespace Import\Importer\Nyc\Students;

use Import\Importer\Nyc\Teachers\TeacherRegistry;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class StudentRegistryFactory
 */
class StudentRegistryFactory implements FactoryInterface
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
        $userService = $serviceLocator->get('User\Service\UserSerivce');
        return new TeacherRegistry($userService);
    }
}
