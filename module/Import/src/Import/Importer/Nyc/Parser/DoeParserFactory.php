<?php

namespace Import\Importer\Nyc\Parser;

use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use Security\Service\SecurityService;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class DoeParserFactory
 *
 * ${CARET}
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
        $userGroupService = $serviceLocator->get('Group\Service\UserGroupService');

        /** @var ClassRoomRegistry $classRegistry */
        $classRegistry    = $serviceLocator->get('Nyc\ClassRegistry');

        /** @var TeacherRegistry $teacherRegistry */
        $teacherRegistry  = $serviceLocator->get('Nyc\TeacherRegistry');

        /** @var StudentRegistry $studentRegistry */
        $studentRegistry  = $serviceLocator->get('Nyc\StudentRegistry');

        /** @var SecurityService $securityService */
        $securityService  = $serviceLocator->get('Security\Service\SecurityService');

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
