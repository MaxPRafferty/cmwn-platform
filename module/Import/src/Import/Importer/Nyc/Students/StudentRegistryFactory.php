<?php

namespace Import\Importer\Nyc\Students;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class StudentRegistryFactory
 */
class StudentRegistryFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new StudentRegistry($container->get(UserServiceInterface::class));
    }
}
