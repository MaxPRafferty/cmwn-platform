<?php

namespace Security\Service;

use Zend\Db\TableGateway\TableGateway;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * Class SecurityServiceFactory
 * @package Security\Service
 * @codeCoverageIgnore
 */
class SecurityServiceFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return mixed
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var TableGateway $tableGateway */
        $tableGateway = $serviceLocator->get('UsersTable');
        return new SecurityService($tableGateway);
    }

}
