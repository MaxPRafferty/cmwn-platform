<?php

namespace Import\Importer\Nyc\Parser;

use Group\Service\GroupServiceInterface;
use Group\Service\UserGroupServiceInterface;
use Import\Importer\Nyc\ClassRoom\ClassRoomRegistry;
use Import\Importer\Nyc\Students\StudentRegistry;
use Import\Importer\Nyc\Teachers\TeacherRegistry;
use Interop\Container\ContainerInterface;
use Security\Service\SecurityServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class DoeParserFactory
 */
class DoeParserFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new DoeParser(
            $container->get(ClassRoomRegistry::class),
            $container->get(TeacherRegistry::class),
            $container->get(StudentRegistry::class),
            $container->get(UserGroupServiceInterface::class),
            $container->get(GroupServiceInterface::class),
            $container->get(SecurityServiceInterface::class)
        );
    }
}
