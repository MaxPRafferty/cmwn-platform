<?php

namespace Import\Importer\Nyc\ClassRoom;

use Group\Service\GroupServiceInterface;
use Interop\Container\ContainerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class ClassRoomRegistryFactory
 */
class ClassRoomRegistryFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new ClassRoomRegistry($container->get(GroupServiceInterface::class));
    }
}
