<?php

namespace Import\Importer\Nyc\Teachers;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class TeacherRegistryFactory
 */
class TeacherRegistryFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new TeacherRegistry($container->get(UserServiceInterface::class));
    }
}
