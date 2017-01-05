<?php

namespace Job\Controller;

use Interop\Container\ContainerInterface;
use Zend\Authentication\AuthenticationServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;

/**
 * Class WorkerControllerFactory
 */
class WorkerControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new WorkerController($container, $container->get(AuthenticationServiceInterface::class));
    }
}
