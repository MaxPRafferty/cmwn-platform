<?php

namespace Import\Importer\Nyc\ClassRoom;

use Group\Service\GroupServiceInterface;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class ClassRoomRegistryFactory
 *
 * ${CARET}
 */
class ClassRoomRegistryFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var GroupServiceInterface $groupService */
        $groupService = $serviceLocator->get('Group\GroupService');
        return new ClassRoomRegistry($groupService);
    }
}
