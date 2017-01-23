<?php

namespace Security\Service;

use Interop\Container\ContainerInterface;
use User\Service\UserServiceInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class SecurityGroupServiceFactory
 */
class SecurityGroupServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new SecurityGroupService(
            new TableGateway('user_groups', $container->get(Adapter::class)),
            $container->get(UserServiceInterface::class)
        );
    }
}
