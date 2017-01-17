<?php

namespace Security\Controller;

use Interop\Container\ContainerInterface;
use Security\Service\SecurityServiceInterface;
use User\Service\UserServiceInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserControllerFactory
 */
class UserControllerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserController(
            $container->get(SecurityServiceInterface::class),
            $container->get(UserServiceInterface::class)
        );
    }
}
