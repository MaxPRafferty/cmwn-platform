<?php

namespace Group\Service;

use Interop\Container\ContainerInterface;
use Zend\Db\Adapter\Adapter;
use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class UserGroupServiceFactory
 */
class UserGroupServiceFactory implements FactoryInterface
{
    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        return new UserGroupService(
            new TableGateway('user_groups', $container->get(Adapter::class))
        );
    }
}
