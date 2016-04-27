<?php

namespace Import\Importer\Nyc\Parser;

use Group\Service\UserGroupServiceInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use Security\Service\SecurityService;
use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoeParserFactory
 */
class DoeParserFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var UserGroupServiceInterface $userGroupService */
        $userGroupService = $serviceLocator->get(UserGroupServiceInterface::class);

        /** @var ClassRoomRegistry $classRegistry */
        $classRegistry    = $serviceLocator->get(ClassRoomRegistry::class);

        /** @var TeacherRegistry $teacherRegistry */
        $teacherRegistry  = $serviceLocator->get(TeacherRegistry::class);

        /** @var StudentRegistry $studentRegistry */
        $studentRegistry  = $serviceLocator->get(StudentRegistry::class);

        /** @var SecurityService $securityService */
        $securityService  = $serviceLocator->get(SecurityServiceInterface::class);

        return new DoeParser(
            $classRegistry,
            $teacherRegistry,
            $studentRegistry,
            $userGroupService,
            $classRegistry->getGroupService(),
            $securityService
        );
    }
}
