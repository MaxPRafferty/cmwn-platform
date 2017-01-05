<?php

namespace Security\Factory;

use Group\Service\UserGroupServiceInterface;
use Interop\Container\ContainerInterface;
use Security\Listeners\UserServiceListener;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserServiceListenerFactory
 */
class UserServiceListenerFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserServiceListener($container->get(UserGroupServiceInterface::class));
    }
}
